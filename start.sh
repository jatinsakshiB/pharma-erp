#!/bin/bash
# ==============================================================================
# DRXStore Launcher (macOS / Linux)
# Port: 5051
# ==============================================================================

PORT=5051
URL="http://127.0.0.1:${PORT}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DATA_DIR="${SCRIPT_DIR}/data"
PID_FILE="${DATA_DIR}/server.pid"
LOG_FILE="${DATA_DIR}/server.log"

mkdir -p "${DATA_DIR}"

echo "=================================================="
echo "    DRXStore - Pharmacy Management System"
echo "=================================================="

# 1. Kill any existing process on port 5051
OLD_PID=$(lsof -ti:${PORT} 2>/dev/null)
if [ -n "${OLD_PID}" ]; then
    echo "[!] Port ${PORT} is occupied by PID ${OLD_PID}."
    echo "[*] Killing existing process on port ${PORT}..."
    kill -9 ${OLD_PID} 2>/dev/null
    sleep 1
    echo "[✓] Port ${PORT} freed."
fi

# 2. Check for PHP binary
PHP_BIN=""
if [ -f "${SCRIPT_DIR}/php/bin/php" ]; then
    PHP_BIN="${SCRIPT_DIR}/php/bin/php"
elif command -v php >/dev/null 2>&1; then
    PHP_BIN="$(command -v php)"
fi

if [ -z "${PHP_BIN}" ]; then
    echo "[X] Error: PHP is not found on your system."
    echo "    Please install PHP (e.g. brew install php OR sudo apt install php)"
    exit 1
fi

PHP_VER=$(${PHP_BIN} -r 'echo PHP_VERSION;')
echo "[i] Using PHP: ${PHP_BIN} (${PHP_VER})"
echo "[*] Starting PHP server on ${URL}..."

# 3. Start PHP built-in server in background
cd "${SCRIPT_DIR}"
${PHP_BIN} -S "127.0.0.1:${PORT}" -t "${SCRIPT_DIR}" > "${LOG_FILE}" 2>&1 &
SERVER_PID=$!
echo "${SERVER_PID}" > "${PID_FILE}"

# 4. Wait for server to become responsive
READY=0
for i in {1..20}; do
    if curl -s -o /dev/null -w "%{http_code}" "${URL}" >/dev/null 2>&1; then
        READY=1
        break
    fi
    sleep 0.25
done

if [ ${READY} -eq 1 ]; then
    echo "[✓] DRXStore server started successfully! (PID: ${SERVER_PID})"
    echo "[*] Opening browser at ${URL}..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        open "${URL}"
    elif command -v xdg-open >/dev/null 2>&1; then
        xdg-open "${URL}" >/dev/null 2>&1 &
    elif command -v sensible-browser >/dev/null 2>&1; then
        sensible-browser "${URL}" >/dev/null 2>&1 &
    fi
else
    echo "[!] Server started, opening browser..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        open "${URL}"
    elif command -v xdg-open >/dev/null 2>&1; then
        xdg-open "${URL}" >/dev/null 2>&1 &
    fi
fi

echo "=================================================="
echo " DRXStore is RUNNING at: ${URL}"
echo " To stop the server, run: ./stop.sh"
echo " Logs are saved in: data/server.log"
echo "=================================================="
