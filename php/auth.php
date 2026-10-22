
<?php
// Configure session cookies before starting the session.
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.use_strict_mode", "1");
    ini_set("session.use_only_cookies", "1");

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);

    session_start();
}

// Log out users after 15 minutes of inactivity.
$timeout = 900;

if (isset($_SESSION["student_id"])) {
    if (
        isset($_SESSION["last_activity"]) &&
        time() - $_SESSION["last_activity"] > $timeout
    ) {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(session_name(), "", [
                "expires" => time() - 42000,
                "path" => $params["path"],
                "domain" => $params["domain"],
                "secure" => $params["secure"],
                "httponly" => $params["httponly"],
                "samesite" => $params["samesite"]
            ]);
        }

        session_destroy();

        header("Location: ../html/login.html?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
}
?>
