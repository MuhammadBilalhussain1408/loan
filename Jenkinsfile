pipeline {
    agent any
    environment {
        DB_DATABASE     = credentials('testing-database-name')
        DB_USERNAME     = credentials('mysql-testing-user')
        DB_PASSWORD = credentials('mysql-testing-password')
        PRODUCTION_SSH_USERNAME = credentials('production-ssh-username')
        PRODUCTION_IP = credentials('production-ip')
        TIMESTAMP= sh (returnStdout: true, script: 'echo "$(date +"%s")"').trim()
        TARGET_DIR="/var/www/mopado.com"
    }
    stages {
        stage('Build') {
            steps {
                sh 'composer install '
                sh 'cp .env.example .env'
                sh 'cp .env.example .env.testing'
                sh 'php artisan key:generate --force'
                sh 'rm -rf node_modules'
                sh 'npm install'
                sh 'npm run build'
            }
        }
        stage('Deploy') {
            steps {
                sh 'echo "Installing production only dependencies"'
                sh 'composer install --no-dev --optimize-autoloader --no-suggest --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts'
                sh 'rm -rf .env .en.testing aliases ./*.log node_modules phpunit.* storage bootstrap/cache/packages.php bootstrap/cache/services.php tests tmp'
                sh 'tar --exclude-vcs -czf "${TIMESTAMP}.tgz" -- *'
                sh 'scp "${TIMESTAMP}.tgz" "${PRODUCTION_SSH_USERNAME}"@"${PRODUCTION_IP}":"${TARGET_DIR}"'
                sh 'rm "${TIMESTAMP}.tgz"'
                sh '''ssh  "${PRODUCTION_SSH_USERNAME}"@"${PRODUCTION_IP}" << EOF
                    cd $TARGET_DIR &&
                    echo "Extracting tarball to releases/${TIMESTAMP}..." &&
                    mkdir -p "releases/${TIMESTAMP}"  &&
                    tar -xzf "${TIMESTAMP}.tgz" --directory "releases/${TIMESTAMP}"  &&
                    rm "${TIMESTAMP}.tgz" &&
                    echo "Symlinking shared resources..." &&
                    ln -s "${TARGET_DIR}/shared/.env" "${TARGET_DIR}/releases/${TIMESTAMP}/.env" &&
                    ln -s "${TARGET_DIR}/shared/storage" "${TARGET_DIR}/releases/${TIMESTAMP}/storage" &&
                    php "releases/${TIMESTAMP}/artisan" migrate --force  &&
                    php "releases/${TIMESTAMP}/artisan" cache:clear &&
                    php "releases/${TIMESTAMP}/artisan" view:clear &&
                    php "releases/${TIMESTAMP}/artisan" queue:restart &&
                    php "releases/${TIMESTAMP}/artisan" storage:link &&
                    echo "Updating current symlink..." &&
                    ln -sfrn "${TARGET_DIR}/releases/${TIMESTAMP}" "${TARGET_DIR}/current" &&
                    sudo chown www-data:www-data -R "${TARGET_DIR}" &&
                    sudo systemctl restart php8.1-fpm &&
                    sudo systemctl reload nginx &&
                    echo "Rolling off old releases..." &&
                    cd releases && ls -1 | sort -r | tail -n +6 | xargs sudo rm -rf &&
                    exit
                    EOF'''
                sh 'echo Deployed successfully'
            }
        }
    }
}
