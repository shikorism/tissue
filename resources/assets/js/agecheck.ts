// TODO: 非ログイン状態で閲覧できるページが全てSPA化された時、年齢確認の実装もSPAに移す
import Cookies from 'js-cookie';

document.addEventListener('DOMContentLoaded', () => {
    if (Cookies.get('agechecked')) {
        document.body.classList.remove('tis-need-agecheck');
    } else {
        const agecheck = document.querySelector<HTMLElement>('.tis-agecheck');
        if (!agecheck) {
            return;
        }
        agecheck.classList.add('show');
        document.body.style.overflow = 'hidden';

        document.querySelector('.tis-agecheck-accept')?.addEventListener('click', () => {
            Cookies.set('agechecked', '1', { expires: 365 });
            agecheck.classList.remove('show');
            document.body.classList.remove('tis-need-agecheck');
            document.body.style.overflow = '';
        });
    }
});
