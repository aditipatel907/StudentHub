<?php
function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function redirect_message(string $message): never {
    header("Location: index.php?message=" . urlencode($message));
    exit;
}

function save_poster(array $file): ?string {
    if (!isset($file["error"]) ||
        $file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Poster upload failed.");
    }

    if ($file["size"] <= 0 ||
        $file["size"] > 2 * 1024 * 1024) {
        throw new RuntimeException(
            "Poster size must not exceed 2 MB."
        );
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime]) ||
        @getimagesize($file["tmp_name"]) === false) {
        throw new RuntimeException(
            "Only valid JPG, PNG, and WEBP images are allowed."
        );
    }

    $directory = __DIR__ . "/uploads";

    if (!is_dir($directory) &&
        !mkdir($directory, 0755, true) &&
        !is_dir($directory)) {
        throw new RuntimeException(
            "Could not create uploads directory."
        );
    }

    $filename = bin2hex(random_bytes(16))
        . "." . $allowed[$mime];

    if (!move_uploaded_file(
        $file["tmp_name"],
        $directory . "/" . $filename
    )) {
        throw new RuntimeException("Could not save poster.");
    }

    return $filename;
}

function remove_poster(?string $filename): void {
    if (!$filename || basename($filename) !== $filename) {
        return;
    }

    $path = __DIR__ . "/uploads/" . $filename;

    if (is_file($path)) {
        unlink($path);
    }
}
?>