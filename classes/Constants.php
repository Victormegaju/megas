<?php
// Application Constants
class Constants {
    // Upload limits
    const MAX_LOGO_SIZE = 2097152; // 2MB in bytes
    const MAX_CHAT_IMAGE_SIZE = 2097152; // 2MB in bytes
    
    // Allowed image types
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Default user expiration (in days)
    const DEFAULT_USER_EXPIRATION = 30;
    const DEFAULT_RESELLER_EXPIRATION = 30;
    
    // Session timeout (in seconds)
    const SESSION_LIFETIME = 7200; // 2 hours
    
    // Chat history limits
    const DEFAULT_CHAT_HISTORY_LIMIT = 50;
    
    // Payment prices (in BRL)
    const PRICE_30_DAYS = 30.00;
    const PRICE_60_DAYS = 55.00;
    const PRICE_90_DAYS = 75.00;
    
    const RESELLER_PRICE_30_DAYS = 50.00;
    const RESELLER_PRICE_60_DAYS = 90.00;
    const RESELLER_PRICE_90_DAYS = 120.00;
}
