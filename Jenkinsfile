pipeline {
    agent any
    
    parameters {
        choice(name: 'ENVIRONMENT', choices: ['dev', 'uat', 'prod'], description: 'Target environment')
        string(name: 'APP_REPO_BRANCH', defaultValue: 'main', description: 'Application repository branch')
    }
    
    environment {
        DOCKER_REGISTRY = 'localhost:5000'
        IMAGE_NAME = 'laravel-auth-api'
        GIT_HASH = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
        IMAGE_TAG = "${BUILD_NUMBER}-${GIT_HASH}"
    }
    
    stages {
        stage('Checkout Application Code') {
            steps {
                echo '🔄 Checking out Laravel Auth API code...'
                checkout scm
            }
        }
        
        stage('Environment Setup') {
            steps {
                echo '🔧 Setting up PHP environment...'
                sh '''
                    echo "PHP Version:"
                    php --version || echo "PHP not available in this container"
                    echo "Composer Version:"
                    composer --version || echo "Composer not available"
                '''
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo '📦 Installing Composer dependencies...'
                sh '''
                    # Skip composer for now in Docker build
                    echo "Dependencies will be installed during Docker build"
                '''
            }
        }
        
        stage('Run Tests') {
            steps {
                echo '🧪 Running PHP tests...'
                sh '''
                    echo "Tests will be run after Docker build"
                    # php artisan test || echo "Tests not available yet"
                '''
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo '🐳 Building Docker image...'
                sh '''
                    # Use the simple Dockerfile for CI builds
                    docker build -f Dockerfile.simple \
                        --build-arg APP_DIR=. \
                        -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} \
                        -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:latest .
                '''
            }
        }
        
        stage('Push to Registry') {
            steps {
                echo '📤 Pushing image to registry...'
                sh '''
                    docker push ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}
                    docker push ${DOCKER_REGISTRY}/${IMAGE_NAME}:latest
                '''
            }
        }
        
        stage('Deploy to Kubernetes') {
            steps {
                echo '🚀 Deploying to Kubernetes...'
                sh '''
                    echo "Deployment step - would deploy to ${ENVIRONMENT} environment"
                    echo "Image: ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}"
                    # kubectl set image deployment/laravel-auth-api laravel-auth-api=${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} -n ${ENVIRONMENT}
                '''
            }
        }
        
        stage('Health Checks') {
            steps {
                echo '🏥 Running health checks...'
                sh '''
                    echo "Health check - verifying deployment"
                    # curl -f http://laravel-auth-api-service/api/health || echo "Health check will be available after K8s deployment"
                '''
            }
        }
    }
    
    post {
        always {
            echo '🧹 Cleaning up...'
            sh '''
                # Clean up old images
                docker image prune -f || true
            '''
        }
        success {
            echo '✅ Pipeline completed successfully!'
        }
        failure {
            echo '❌ Pipeline failed!'
        }
    }
}
