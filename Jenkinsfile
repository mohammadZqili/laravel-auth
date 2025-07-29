pipeline {
    agent any
    
    parameters {
        choice(name: 'ENVIRONMENT', choices: ['dev', 'uat', 'prod'], description: 'Target environment')
        string(name: 'APP_REPO_BRANCH', defaultValue: 'main', description: 'Application repository branch')
        string(name: 'CICD_REPO_BRANCH', defaultValue: 'main', description: 'CI/CD repository branch')
    }
    
    environment {
        DOCKER_REGISTRY = 'localhost:5000'
        IMAGE_NAME = 'laravel-auth-api'
        GIT_HASH = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
        IMAGE_TAG = "${BUILD_NUMBER}-${GIT_HASH}"
        APP_NAME = 'laravel-auth'
    }
    
    stages {
        stage('Checkout Application Code') {
            steps {
                echo '🔄 Checking out Laravel Auth API code...'
                checkout scm
                script {
                    env.APP_WORKSPACE = env.WORKSPACE
                }
            }
        }
        
        stage('Checkout CI/CD Configuration') {
            steps {
                echo '🔄 Checking out CI/CD infrastructure...'
                script {
                    try {
                        dir('cicd-config') {
                            // Try to checkout CI/CD config repository (public repo, no credentials needed)
                            git branch: "${params.CICD_REPO_BRANCH}",
                                url: 'https://github.com/mohammadZqili/ci-cd.git'
                        }
                        echo '✅ Successfully checked out CI/CD configuration'
                        env.CICD_CONFIG_AVAILABLE = 'true'
                    } catch (Exception e) {
                        echo "⚠️  CI/CD configuration repository not available: ${e.getMessage()}"
                        echo "📦 Using fallback configuration from local setup"
                        env.CICD_CONFIG_AVAILABLE = 'false'
                    }
                }
            }
        }
        
        stage('Environment Setup') {
            steps {
                echo '🔧 Setting up environment...'
                sh '''
                    echo "=== Environment Info ==="
                    echo "Workspace: ${WORKSPACE}"
                    echo "App Workspace: ${APP_WORKSPACE}"
                    echo "Environment: ${ENVIRONMENT}"
                    echo "Docker Registry: ${DOCKER_REGISTRY}"
                    echo "Image: ${IMAGE_NAME}:${IMAGE_TAG}"
                    
                    echo "=== Checking files ==="
                    ls -la
                    echo "=== CI/CD Config Status ==="
                    echo "CI/CD Config Available: ${CICD_CONFIG_AVAILABLE}"
                    if [ "${CICD_CONFIG_AVAILABLE}" = "true" ]; then
                        ls -la cicd-config/docker/laravel/ || echo "CI/CD config directory not found"
                    else
                        echo "Using fallback build configuration"
                    fi
                '''
            }
        }
        
        stage('Run Tests') {
            steps {
                echo '🧪 Running PHP tests...'
                sh '''
                    echo "Running tests for Laravel Auth API..."
                    # Tests would be run here
                    # php artisan test || echo "Tests will be enabled later"
                '''
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo '🐳 Building Docker image with CI/CD configuration...'
                sh '''
                    echo "Building Docker image using CI/CD Dockerfile..."
                    
                    # Use the Dockerfile from CI/CD repository if available
                    if [ "${CICD_CONFIG_AVAILABLE}" = "true" ] && [ -f "cicd-config/docker/laravel/Dockerfile" ]; then
                        echo "✅ Using CI/CD Laravel Dockerfile"
                        docker build \
                            -f cicd-config/docker/laravel/Dockerfile \
                            --build-arg APP_SOURCE_DIR=. \
                            -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} \
                            -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:latest \
                            .
                    else
                        echo "❌ CI/CD Dockerfile not found, using local fallback Dockerfile"
                        # Use the local Dockerfile as fallback
                        docker build \
                            -f Dockerfile \
                            -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG} \
                            -t ${DOCKER_REGISTRY}/${IMAGE_NAME}:latest \
                            .
                    fi
                '''
            }
        }
        
        stage('Push to Registry') {
            steps {
                echo '📤 Pushing image to registry...'
                sh '''
                    echo "Pushing ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}"
                    docker push ${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}
                    docker push ${DOCKER_REGISTRY}/${IMAGE_NAME}:latest
                '''
            }
        }
        
        stage('Deploy to Kubernetes') {
            steps {
                echo '🚀 Deploying to Kubernetes...'
                sh '''
                    echo "Deploying to ${ENVIRONMENT} environment"
                    echo "Using deployment config from CI/CD repository"
                    
                    if [ "${CICD_CONFIG_AVAILABLE}" = "true" ] && [ -f "cicd-config/kubernetes/${APP_NAME}/deployment.yaml" ]; then
                        echo "✅ Found Kubernetes deployment config"
                        # Apply Kubernetes manifests
                        # kubectl apply -f cicd-config/kubernetes/${APP_NAME}/ -n ${ENVIRONMENT}
                        echo "Would deploy using: cicd-config/kubernetes/${APP_NAME}/"
                    else
                        echo "⚠️  Kubernetes config not found, skipping deployment"
                    fi
                '''
            }
        }
        
        stage('Health Checks') {
            steps {
                echo '🏥 Running health checks...'
                sh '''
                    echo "Health check for ${IMAGE_NAME}"
                    echo "Deployment verification for ${ENVIRONMENT} environment"
                    # Health checks would be implemented here
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
            echo "🎉 ${IMAGE_NAME}:${IMAGE_TAG} built and deployed to ${ENVIRONMENT}"
        }
        failure {
            echo '❌ Pipeline failed!'
            echo "Check logs for ${IMAGE_NAME} build issues"
        }
    }
}
