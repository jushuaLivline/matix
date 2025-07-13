import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

function scanFiles(dir, typeExtension = '.js', files = []) {
    const fileList = fs.readdirSync(dir);
    for (const file of fileList) {
        const name = `${dir}/${file}`;
        if (fs.statSync(name).isDirectory()) {
            scanFiles(name, typeExtension, files);
        } else {
            if (file.includes(typeExtension)) {
                files.push(name);
            }
        }
    }
    return files;
}

var cssFiles = scanFiles('resources/css', '.css')
var jsFiles = scanFiles('resources/js')

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/admin.scss',
                'resources/sass/custom.scss',
                ...cssFiles,
                ...jsFiles,
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
