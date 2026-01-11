#!/bin/bash
# Installation Verification Script for Megas Chat
# Run this script after uploading files to verify everything is in place

echo "🔍 Verifying Megas Chat Installation..."
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counter for checks
PASSED=0
FAILED=0

# Function to check file exists
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1 exists"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $1 missing"
        ((FAILED++))
    fi
}

# Function to check directory exists and is writable
check_dir() {
    if [ -d "$1" ]; then
        if [ -w "$1" ]; then
            echo -e "${GREEN}✓${NC} $1 exists and is writable"
            ((PASSED++))
        else
            echo -e "${YELLOW}⚠${NC} $1 exists but is NOT writable"
            ((FAILED++))
        fi
    else
        echo -e "${RED}✗${NC} $1 missing"
        ((FAILED++))
    fi
}

echo "📁 Checking Core Files..."
check_file "index.php"
check_file "schema.sql"
check_file "config.template.php"
check_file ".gitignore"

echo ""
echo "📂 Checking Directories..."
check_dir "classes"
check_dir "controllers"
check_dir "views"
check_dir "install"
check_dir "uploads/logo"

echo ""
echo "🔧 Checking Classes..."
check_file "classes/Database.php"
check_file "classes/User.php"
check_file "classes/Settings.php"
check_file "classes/Router.php"
check_file "classes/Constants.php"

echo ""
echo "🎮 Checking Controllers..."
check_file "controllers/AdminController.php"
check_file "controllers/AuthController.php"
check_file "controllers/ApiController.php"
check_file "controllers/PaymentController.php"
check_file "controllers/RevendaController.php"
check_file "controllers/UserController.php"
check_file "controllers/WebhookController.php"

echo ""
echo "👁️ Checking Views..."
check_file "views/login.php"
check_file "views/layout.php"
check_file "views/admin/dashboard.php"
check_file "views/admin/users.php"
check_file "views/admin/resellers.php"
check_file "views/admin/settings.php"
check_file "views/revenda/dashboard.php"
check_file "views/revenda/users.php"
check_file "views/revenda/profile.php"
check_file "views/user/dashboard.php"
check_file "views/user/chat.php"
check_file "views/user/profile.php"
check_file "views/payment/checkout.php"

echo ""
echo "🔗 Checking Special Files..."
check_file "install/index.php"
check_file "appeal/webhooks/mercadopago.php"

echo ""
echo "📚 Checking Documentation..."
check_file "README.md"
check_file "DEPLOYMENT.md"
check_file "PROJECT_SUMMARY.md"
check_file "nginx.conf.example"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "Results: ${GREEN}${PASSED} passed${NC}, ${RED}${FAILED} failed${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Verify Nginx configuration"
    echo "2. Check PHP version (php -v)"
    echo "3. Verify MySQL/MariaDB is running"
    echo "4. Access http://your-domain.com/install"
    echo "5. Follow installation wizard"
    echo ""
    exit 0
else
    echo -e "${RED}✗ Some checks failed!${NC}"
    echo ""
    echo "Please ensure all files are uploaded correctly."
    echo "Check DEPLOYMENT.md for detailed instructions."
    echo ""
    exit 1
fi
