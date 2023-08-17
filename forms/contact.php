<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $message = $_POST['message'];

  $to = 'admin@sync-fast.com';
  $subject = 'New Contact Request';

  $headers = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
  $headers .= "Content-Transfer-Encoding: 8bit\r\n";

  $message = "Name: $name\nEmail: $email\nMessage:\n$message";
  $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

  if (mail($to, $encoded_subject, $message, $headers)) {
    echo 'success';
  } else {
    echo 'An error occurred. Please try again later.';
  }
}
