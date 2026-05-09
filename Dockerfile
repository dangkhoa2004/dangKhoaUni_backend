# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libsodium-dev # <--- QUAN TRỌNG: Thư viện cho jwt-auth

# Xóa cache apt để giảm dung lượng image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt các PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sodium # <--- Thêm sodium ở đây

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Chạy lệnh cài đặt (Thêm --ignore-platform-reqs để tránh lỗi version PHP)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs