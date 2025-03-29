import { twMerge } from 'tailwind-merge';
import classNames from 'classnames';

export function cn(...inputs: classNames.ArgumentArray) {
    return twMerge(classNames(inputs));
}
