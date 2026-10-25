#!/bin/bash
# ==============================================================================
# DRXStore Shutdown Script (macOS / Linux)
# Port: 5051
# ==============================================================================

PORT=5051
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PID_FILE="${SCRIPT_DIR}/data/server.pid"

echo "=================================================="
echo "    Stopping DRXStore Server (Port ${PORT})..."
echo "=================================================="

STOPPED=0

# Try PID file first
if [ -f "${PID_FILE}" ]; then
    SAVED_PID=$(cat "${PID_FILE}")
    if [ -n "${SAVED_PID}" ] && kill -0 "${SAVED_PID}" 2>/dev/null; then
        echo "[*] Killing DRXStore process (PID: ${SAVED_PID})..."
        kill -9 "${SAVED_PID}" 2>/dev/null
        STOPPED=1
    fi
    rm -f "${PID_FILE}"
fi

# Also kill any remaining process bound to port 5051
PORT_PID=$(lsof -ti:${PORT} 2>/dev/null)
if [ -n "${PORT_PID}" ]; then
    echo "[*] Killing remaining process on port ${PORT} (PID: ${PORT_PID})..."
    kill -9 ${PORT_PID} 2>/dev/null
    STOPPED=1
fi

if [ ${STOPPED} -eq 1 ]; then
    echo "[✓] DRXStore server stopped successfully."
else
    echo "[i] No running DRXStore server was found on port ${PORT}."
fi
echo "=================================================="
