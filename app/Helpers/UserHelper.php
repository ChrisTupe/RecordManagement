<?php

/**
 * Check if the current user is an admin
 */
if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}

/**
 * Check if the current user is a regular user
 */
if (!function_exists('isUser')) {
    function isUser(): bool
    {
        return auth()->check() && auth()->user()->isUser();
    }
}

/**
 * Get the current user's role
 */
if (!function_exists('userRole')) {
    function userRole(): ?string
    {
        return auth()->check() ? auth()->user()->role : null;
    }
}
