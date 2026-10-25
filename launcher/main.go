package main

import (
	"bufio"
	"fmt"
	"net"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"
	"syscall"
	"time"
)

const (
	port = 5051
	url  = "http://127.0.0.1:5051"
)

func main() {
	exePath, err := os.Executable()
	if err != nil {
		exePath = "."
	}
	appDir := filepath.Dir(exePath)
	dataDir := filepath.Join(appDir, "data")
	_ = os.MkdirAll(dataDir, 0755)

	// 1. Kill any existing process on port 5051
	freePort(port)

	// 2. Locate PHP
	phpBin, phpIni := findPHP(appDir)
	if phpBin == "" {
		showError("PHP Runtime Not Found",
			"Could not find PHP runtime.\nPlease make sure the 'php' folder exists in the DRXStore folder, or that PHP is installed in your PATH.")
		os.Exit(1)
	}

	// 3. Start PHP Server
	args := []string{}
	if phpIni != "" {
		args = append(args, "-c", phpIni)
	}
	args = append(args, "-S", fmt.Sprintf("127.0.0.1:%d", port), "-t", appDir)

	cmd := exec.Command(phpBin, args...)
	cmd.Dir = appDir

	// Hide console window in Windows
	cmd.SysProcAttr = &syscall.SysProcAttr{
		HideWindow:    true,
		CreationFlags: 0x08000000 | 0x00000200, // CREATE_NO_WINDOW | CREATE_NEW_PROCESS_GROUP
	}

	logFile, err := os.OpenFile(filepath.Join(dataDir, "server.log"), os.O_CREATE|os.O_WRONLY|os.O_TRUNC, 0644)
	if err == nil {
		cmd.Stdout = logFile
		cmd.Stderr = logFile
	}

	if err := cmd.Start(); err != nil {
		showError("Failed to Start Server", fmt.Sprintf("Error launching PHP server: %v", err))
		os.Exit(1)
	}

	// Save PID
	if cmd.Process != nil {
		_ = os.WriteFile(filepath.Join(dataDir, "server.pid"), []byte(strconv.Itoa(cmd.Process.Pid)), 0644)
	}

	// 4. Wait for server to respond
	waitForServer(url, 10*time.Second)

	// 5. Open Default Browser
	openBrowser(url)
}

func freePort(p int) {
	// Check if port is in use
	conn, err := net.DialTimeout("tcp", fmt.Sprintf("127.0.0.1:%d", p), 300*time.Millisecond)
	if err == nil {
		conn.Close()
		// Port is in use - find PID via netstat
		pids := findPIDsOnPort(p)
		for _, pid := range pids {
			killCmd := exec.Command("taskkill", "/F", "/PID", strconv.Itoa(pid))
			killCmd.SysProcAttr = &syscall.SysProcAttr{HideWindow: true, CreationFlags: 0x08000000}
			_ = killCmd.Run()
		}
		time.Sleep(500 * time.Millisecond)
	}
}

func findPIDsOnPort(p int) []int {
	var pids []int
	cmd := exec.Command("netstat", "-ano", "-p", "tcp")
	cmd.SysProcAttr = &syscall.SysProcAttr{HideWindow: true, CreationFlags: 0x08000000}
	out, err := cmd.Output()
	if err != nil {
		return pids
	}

	portSuffix := fmt.Sprintf(":%d", p)
	scanner := bufio.NewScanner(strings.NewReader(string(out)))
	for scanner.Scan() {
		line := strings.TrimSpace(scanner.Text())
		if strings.Contains(line, "LISTENING") && strings.Contains(line, portSuffix) {
			fields := strings.Fields(line)
			if len(fields) >= 5 {
				pidStr := fields[len(fields)-1]
				if pid, err := strconv.Atoi(pidStr); err == nil && pid > 0 {
					pids = append(pids, pid)
				}
			}
		}
	}
	return pids
}

func findPHP(appDir string) (string, string) {
	// Check bundled php
	candidates := []string{
		filepath.Join(appDir, "php", "php.exe"),
		filepath.Join(appDir, "php", "php-win.exe"),
	}
	for _, c := range candidates {
		if _, err := os.Stat(c); err == nil {
			ini := filepath.Join(appDir, "php", "php.ini")
			if _, err := os.Stat(ini); err == nil {
				return c, ini
			}
			return c, ""
		}
	}

	// Check system PATH
	if p, err := exec.LookPath("php.exe"); err == nil {
		return p, ""
	}
	if p, err := exec.LookPath("php"); err == nil {
		return p, ""
	}
	return "", ""
}

func waitForServer(targetUrl string, timeout time.Duration) {
	deadline := time.Now().Add(timeout)
	client := http.Client{Timeout: 500 * time.Millisecond}
	for time.Now().Before(deadline) {
		resp, err := client.Get(targetUrl)
		if err == nil {
			resp.Body.Close()
			return
		}
		time.Sleep(200 * time.Millisecond)
	}
}

func openBrowser(targetUrl string) {
	cmd := exec.Command("rundll32", "url.dll,FileProtocolHandler", targetUrl)
	cmd.SysProcAttr = &syscall.SysProcAttr{HideWindow: true, CreationFlags: 0x08000000}
	_ = cmd.Start()
}

func showError(title, msg string) {
	// Use PowerShell or VBScript msgbox if on Windows
	psCmd := fmt.Sprintf(`Add-Type -AssemblyName System.Windows.Forms; [System.Windows.Forms.MessageBox]::Show('%s', '%s', 'OK', 'Error')`,
		strings.ReplaceAll(msg, "'", "''"), strings.ReplaceAll(title, "'", "''"))
	cmd := exec.Command("powershell", "-NoProfile", "-Command", psCmd)
	cmd.SysProcAttr = &syscall.SysProcAttr{HideWindow: true, CreationFlags: 0x08000000}
	_ = cmd.Run()
}
