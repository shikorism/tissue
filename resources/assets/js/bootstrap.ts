// jQuery
import './tissue';

// Setup global request header
const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
if (!token) {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
} else {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token.content
        }
    });
}

// Bootstrap
import 'bootstrap';
