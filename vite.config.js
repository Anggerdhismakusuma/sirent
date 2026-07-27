import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        // host: '0.0.0.0',
        // port: 5137,
        // strictPort:true,
        // cors: {
        //     origin: [ 
        //     'http://localhost:8000',
        //     'http://127.0.0.1:8000',
        //     'http://10.201.238.237:8000',
        // ]
        // },
        // hmr: {
        //     host: '10.201.238.237', // Ganti dengan IP IPv4 laptop kamu
        // },
    },
});
