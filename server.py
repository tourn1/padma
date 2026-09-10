#!/usr/bin/env python3
"""
server.py — Servidor de desarrollo local para proyectos PHP
============================================================
Uso:
    python3 server.py             # Arranca en localhost:8080
    python3 server.py 8000        # Arranca en el puerto indicado
    python3 server.py 8000 0.0.0.0  # Accesible en la red local

El script:
  1. Verifica si PHP está instalado en el sistema.
  2. Si no está, lo instala automáticamente usando Homebrew (macOS).
  3. Levanta el servidor built-in de PHP apuntando al directorio actual.
"""

import subprocess
import sys
import os
import shutil
import platform

# ─── Configuración ────────────────────────────────────────────────────────────
DEFAULT_HOST = "localhost"
DEFAULT_PORT = 8080
DOCUMENT_ROOT = os.path.dirname(os.path.abspath(__file__))
# ──────────────────────────────────────────────────────────────────────────────


def color(text: str, code: str) -> str:
    """Aplica color ANSI si la terminal lo soporta."""
    if sys.stdout.isatty():
        return f"\033[{code}m{text}\033[0m"
    return text


def info(msg: str):
    print(color(f"  [INFO]  {msg}", "96"))


def success(msg: str):
    print(color(f"  [OK]    {msg}", "92"))


def warn(msg: str):
    print(color(f"  [WARN]  {msg}", "93"))


def error(msg: str):
    print(color(f"  [ERROR] {msg}", "91"))


def step(msg: str):
    print(color(f"\n▶ {msg}", "1;97"))


# ─── 1. Verificar PHP ─────────────────────────────────────────────────────────

def find_php():
    """Devuelve la ruta al binario php, o None si no se encuentra."""
    php = shutil.which("php")
    if php:
        return php

    homebrew_paths = [
        "/opt/homebrew/bin/php",
        "/usr/local/bin/php",
        "/opt/homebrew/opt/php/bin/php",
    ]
    for path in homebrew_paths:
        if os.path.isfile(path) and os.access(path, os.X_OK):
            return path

    return None


def get_php_version(php_bin: str) -> str:
    """Obtiene la versión de PHP."""
    try:
        result = subprocess.run(
            [php_bin, "--version"],
            capture_output=True, text=True, timeout=10
        )
        first_line = result.stdout.splitlines()[0] if result.stdout else "desconocida"
        return first_line
    except Exception:
        return "desconocida"


# ─── 2. Instalar PHP via Homebrew ─────────────────────────────────────────────

def install_php_homebrew():
    """Instala PHP usando Homebrew. Devuelve la ruta al binario o None."""
    brew = shutil.which("brew")
    if not brew:
        for p in ["/opt/homebrew/bin/brew", "/usr/local/bin/brew"]:
            if os.path.isfile(p):
                brew = p
                break

    if not brew:
        error("Homebrew no está instalado.")
        warn("Instalá Homebrew desde https://brew.sh y volvé a ejecutar este script.")
        return None

    step("Instalando PHP via Homebrew (puede tardar unos minutos)...")
    info(f"Usando brew en: {brew}")

    try:
        process = subprocess.Popen(
            [brew, "install", "php"],
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            text=True,
            bufsize=1,
        )
        for line in process.stdout:
            stripped = line.rstrip()
            if stripped:
                print(f"    {stripped}")
        process.wait()

        if process.returncode != 0:
            error("La instalación de PHP falló.")
            return None

        success("PHP instalado correctamente.")

        new_paths = ["/opt/homebrew/bin", "/usr/local/bin"]
        for p in new_paths:
            if p not in os.environ.get("PATH", ""):
                os.environ["PATH"] = p + ":" + os.environ.get("PATH", "")

        return find_php()

    except Exception as e:
        error(f"Error durante la instalación: {e}")
        return None


# ─── 3. Levantar el servidor ──────────────────────────────────────────────────

def start_server(php_bin: str, host: str, port: int):
    """Levanta el servidor built-in de PHP."""
    address = f"{host}:{port}"
    url = f"http://{host}:{port}"

    step(f"Levantando servidor PHP en {url}")
    info(f"Document root : {DOCUMENT_ROOT}")
    info(f"PHP binario   : {php_bin}")
    info(f"Versión PHP   : {get_php_version(php_bin)}")
    print()
    print(color(f"  🌐  Accedé al sitio en: {url}", "1;96"))
    print(color("  Presioná Ctrl+C para detener el servidor.\n", "90"))

    cmd = [
        php_bin, "-S", address, "-t", DOCUMENT_ROOT,
        "-d", "upload_max_filesize=128M",
        "-d", "post_max_size=256M",
        "-d", "max_execution_time=300",
        "-d", "max_input_time=300",
    ]

    server_proc = None
    try:
        server_proc = subprocess.Popen(
            cmd,
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            text=True,
            bufsize=1,
        )

        for line in server_proc.stdout:
            stripped = line.rstrip()
            if stripped:
                if " 200 " in stripped or "Accepted" in stripped:
                    print(color(f"  {stripped}", "32"))
                elif any(code in stripped for code in [" 404 ", " 500 ", " 403 "]):
                    print(color(f"  {stripped}", "33"))
                elif "error" in stripped.lower() or "Error" in stripped:
                    print(color(f"  {stripped}", "91"))
                else:
                    print(color(f"  {stripped}", "90"))

    except KeyboardInterrupt:
        print()
        warn("Servidor detenido por el usuario.")
    finally:
        if server_proc and server_proc.poll() is None:
            server_proc.terminate()
            server_proc.wait()
        success("¡Hasta luego!")


# ─── Main ─────────────────────────────────────────────────────────────────────

def main():
    args = sys.argv[1:]
    port = DEFAULT_PORT
    host = DEFAULT_HOST

    if len(args) >= 1:
        try:
            port = int(args[0])
        except ValueError:
            error(f"Puerto inválido: '{args[0]}'. Usando {DEFAULT_PORT}.")
    if len(args) >= 2:
        host = args[1]

    print()
    print(color("╔══════════════════════════════════════════╗", "1;35"))
    print(color("║     🐘  PADMA — Servidor PHP Local       ║", "1;35"))
    print(color("╚══════════════════════════════════════════╝", "1;35"))
    print()

    if platform.system() != "Darwin":
        warn("Este script está optimizado para macOS.")
        warn("En Linux/Windows instalá PHP manualmente y volvé a ejecutarlo.")

    step("Verificando instalación de PHP...")
    php_bin = find_php()

    if php_bin:
        success(f"PHP encontrado en: {php_bin}")
    else:
        warn("PHP no está instalado.")

        if platform.system() == "Darwin":
            respuesta = input(
                color("\n  ¿Querés instalarlo ahora con Homebrew? [s/N]: ", "93")
            ).strip().lower()
            if respuesta in ("s", "si", "sí", "y", "yes"):
                php_bin = install_php_homebrew()
            else:
                error("PHP es necesario para ejecutar el servidor. Abortando.")
                sys.exit(1)
        else:
            error("Instalá PHP manualmente y volvé a ejecutar este script.")
            sys.exit(1)

    if not php_bin:
        error("No se pudo encontrar ni instalar PHP. Abortando.")
        sys.exit(1)

    start_server(php_bin, host, port)


if __name__ == "__main__":
    main()
