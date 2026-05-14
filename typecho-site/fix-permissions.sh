#!/bin/sh

# 强制重新安装（默认 true，每次启动都会清空旧配置）
FORCE_REINSTALL="${FORCE_REINSTALL:-true}"

# 确保 uploads 目录存在且可写
mkdir -p /var/www/html/usr/uploads
chown -R www-data:www-data /var/www/html/usr/uploads 2>/dev/null || true
chmod -R 775 /var/www/html/usr/uploads 2>/dev/null || true

# 如果开启了强制重装，删除旧的 config.inc.php
if [ "$FORCE_REINSTALL" = "true" ]; then
    echo "Force reinstall enabled, removing config.inc.php ..."
    rm -f /var/www/html/config.inc.php
fi

# 如果配置文件存在，修正权限
if [ -f /var/www/html/config.inc.php ]; then
    chown www-data:www-data /var/www/html/config.inc.php 2>/dev/null || true
    chmod 664 /var/www/html/config.inc.php 2>/dev/null || true
fi

# 安装过程中站点根目录需要可写（生成配置文件）
chmod 777 /var/www/html

exec "$@"
