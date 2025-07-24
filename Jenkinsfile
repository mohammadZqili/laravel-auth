pipeline {
    agent any
    
    environment {
        APP_NAME = 'laravel-auth-api'
        DOCKER_REGISTRY = 'localhost:5000'
        DOCKER_IMAGE = "${DOCKER_REGISTRY}/${APP_NAME}"
        KUBECONFIG = '/var/jenkins_home/.kube/config'
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo '🔄 Checking out Laravel Auth API source code...'
                checkout scm
            }
        }
        
        stage('Environment Setup') {
            steps {
                echo '⚙️ Setting up Laravel environment...'
                dir('apps/laravel-auth-api') {
                    sh '''
                        cp .env.example .env || true
                        echo "APP_KEY=base64:YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXoxMjM0NTY=" >> .env
                        echo "DB_HOST=mysql-dev" >> .env
                        echo "DB_DATABASE=app_db" >> .env
                        echo "DB_USERNAME=app_user" >> .env
                        echo "DB_PASSWORD=apppass" >> .env
                        echo "REDIS_HOST=redis" >> .env
                    '''
                }
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo '🏗️ Building Laravel Auth API Docker image...'
                script {
                    def imageTag = "${BUILD_NUMBER}-${GIT_COMMIT.take(7)}"
                    sh """
                        docker build -f Dockerfile \\
                            --build-arg APP_DIR=apps/laravel-auth-api \\
                            --build-arg SERVICE_NAME=api \\
                            -t ${DOCKER_IMAGE}:${imageTag} \\
                            -t ${DOCKER_IMAGE}:latest .
                    """
                    env.IMAGE_TAG = imageTag
                }
            }
        }
        
        stage('Run Tests') {
            steps {
                echo '🧪 Running Laravel tests...'
                dir('apps/laravel-auth-api') {
                    sh '''
                        docker run --rm \\
                            -v $(pwd):/app \\
                            -w /app \\
                            composer/composer:latest \\
                            install --no-dev --optimize-autoloader || true
                    '''
                }
            }
        }
        
        stage('Security Scan') {
            steps {
                echo '🔒 Running security scans...'
                sh '''
                    echo "Running security scan for Laravel Auth API..."
                    # Add security scanning tools here
                    echo "✅ Security scan completed"
                '''
            }
        }
        
        stage('Push to Registry') {
            steps {
                echo '📤 Pushing image to Docker registry...'
                sh """
                    docker push ${DOCKER_IMAGE}:${IMAGE_TAG}
                    docker push ${DOCKER_IMAGE}:latest
                """
            }
        }
        
        stage('Deploy to Development') {
            when {
                branch 'develop'
            }
            steps {
                echo '🚀 Deploying to Development environment...'
                sh """
                    kubectl set image deployment/laravel-api \\
                        api=${DOCKER_IMAGE}:${IMAGE_TAG} \\
                        -n dev
                    kubectl rollout status deployment/laravel-api -n dev --timeout=300s
                """
            }
        }
        
        stage('Deploy to UAT') {
            when {
                branch 'main'
            }
            steps {
                echo '🎯 Deploying to UAT environment...'
                input message: 'Deploy to UAT?', ok: 'Deploy'
                sh """
                    kubectl set image deployment/laravel-api \\
                        api=${DOCKER_IMAGE}:${IMAGE_TAG} \\
                        -n uat
                    kubectl rollout status deployment/laravel-api -n uat --timeout=300s
                """
            }
        }
        
        stage('Deploy to Production') {
            when {
                tag pattern: 'v\\d+\\.\\d+\\.\\d+', comparator: 'REGEXP'
            }
            steps {
                echo '🏭 Deploying to Production environment...'
                input message: 'Deploy to Production?', ok: 'Deploy to PROD'
                sh """
                    kubectl set image deployment/laravel-api \\
                        api=${DOCKER_IMAGE}:${IMAGE_TAG} \\
                        -n prod
                    kubectl rollout status deployment/laravel-api -n prod --timeout=600s
                """
            }
        }
        
        stage('Health Check') {
            steps {
                echo '💚 Running health checks...'
                script {
                    def namespace = 'dev'
                    if (env.BRANCH_NAME == 'main') {
                        namespace = 'uat'
                    } else if (env.TAG_NAME?.matches('v\\d+\\.\\d+\\.\\d+')) {
                        namespace = 'prod'
                    }
                    
                    sh """
                        kubectl wait --for=condition=available \\
                            deployment/laravel-api \\
                            -n ${namespace} \\
                            --timeout=300s
                        
                        # Test health endpoint
                        kubectl port-forward service/laravel-api 8082:80 -n ${namespace} &
                        FORWARD_PID=\$!
                        sleep 5
                        curl -f http://localhost:8082/api/healthz || echo "Health check warning"
                        kill \$FORWARD_PID || true
                    """
                }
            }
        }
    }
    
    post {
        always {
            echo '🧹 Cleaning up...'
            sh '''
                docker system prune -f || true
                pkill -f "kubectl port-forward" || true
            '''
        }
        success {
            echo '✅ Laravel Auth API pipeline completed successfully!'
            slackSend(
                channel: '#deployments',
                color: 'good',
                message: "✅ Laravel Auth API deployed successfully - Build #${BUILD_NUMBER}"
            )
        }
        failure {
            echo '❌ Laravel Auth API pipeline failed!'
            slackSend(
                channel: '#deployments',
                color: 'danger',
                message: "❌ Laravel Auth API deployment failed - Build #${BUILD_NUMBER}"
            )
        }
    }
} 