<style>
    body {
        font-family: 'Figtree', sans-serif;
    }

    textarea::-webkit-scrollbar {
        width: 4px;
    }

    textarea::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }

    textarea::-webkit-scrollbar-thumb {
        background: #c9c9c9;
        border-radius: 4px;
    }

    textarea::-webkit-scrollbar-thumb:hover {
        background: #828282;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-enter {
        animation: slideIn 0.3s ease-out;
    }

    .toast-exit {
        animation: slideOut 0.3s ease-out;
    }
</style>