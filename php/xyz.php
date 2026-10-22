<?php
$hashedPassword = password_hash($pwd, 'admin@12345');
 echo "<script>
            alert('Registration successful! Your Enrollment No. is $hashedPassword');
            window.location.href = '../html/login.html';
          </script>";
?>