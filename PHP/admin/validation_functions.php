<?php

function validateUsername($username) {
    if (strlen($username) <= 3) {
        return 'Username should be longer than 3 characters.';
    }

    if (!preg_match('/^[a-zA-Z]/', $username)) {
        return 'Username must start with an alphabet.';
    }

    if (!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
        return 'Username can only contain alphabets, numbers, underscore (_), and dot (.).';
    }

    return null; 
}


function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }
    return null;
}


function validatePassword($password) {
    if (strlen($password) < 6) {
        return 'Password should be at least 6 characters long.';
    }

    if (strlen($password) > 12) {
        return 'Password cannot be longer than 12 characters.';
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        return 'Password must contain at least one letter.';
    }

    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }

    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return 'Password must contain at least one special character.';
    }

    return null; 
}

function validateRole($role) {
    $allowed_roles = ['admin', 'customer']; 
    if (!in_array($role, $allowed_roles)) {
        return 'Invalid role. Role must be either "admin" or "customer".';
    }
    return null; 
}

?>