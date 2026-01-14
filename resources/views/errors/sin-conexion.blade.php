<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Conexión - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 80px;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        h1 {
            font-size: 1.75rem;
            color: #2d3748;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        p {
            font-size: 1rem;
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .retry-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .retry-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .retry-button:active {
            transform: translateY(0);
        }

        .retry-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .status {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #a0aec0;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>Sin Conexión</h1>
        <p>Lo sentimos, no se puede conectar al servidor. Por favor, verifica tu conexión a internet e intenta nuevamente.</p>
        
        <button class="retry-button" id="retryButton" onclick="checkConnection()">
            Intentar de Nuevo
        </button>

        <div class="status" id="statusMessage"></div>
    </div>

    <script>
        let isChecking = false;

        async function checkConnection() {
            if (isChecking) return;
            
            isChecking = true;
            const button = document.getElementById('retryButton');
            const statusMessage = document.getElementById('statusMessage');
            
            button.disabled = true;
            button.innerHTML = 'Verificando<span class="spinner"></span>';
            statusMessage.textContent = 'Comprobando conexión...';

            try {
                const response = await fetch('/api/network/status');
                const data = await response.json();

                if (data.connected) {
                    statusMessage.textContent = '✓ Conexión restablecida. Redirigiendo...';
                    statusMessage.style.color = '#48bb78';
                    
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1000);
                } else {
                    statusMessage.textContent = 'Aún sin conexión. Intenta nuevamente.';
                    statusMessage.style.color = '#f56565';
                    button.disabled = false;
                    button.innerHTML = 'Intentar de Nuevo';
                }
            } catch (error) {
                statusMessage.textContent = 'No se pudo verificar la conexión.';
                statusMessage.style.color = '#f56565';
                button.disabled = false;
                button.innerHTML = 'Intentar de Nuevo';
            }

            isChecking = false;
        }

        // Verificar automáticamente cada 10 segundos
        setInterval(() => {
            if (!isChecking) {
                checkConnection();
            }
        }, 10000);

        // Verificar cuando la ventana recupera el foco
        window.addEventListener('focus', () => {
            if (!isChecking) {
                checkConnection();
            }
        });
    </script>
</body>
</html>
