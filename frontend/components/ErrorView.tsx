import React from 'react';
import { ResponseError } from '../api/errors';

const DEFAULT_MESSAGE = 'エラーが発生しました。しばらくしてからもう一度お試しください。';

interface ErrorViewProps {
    title?: string;
    subtitle?: string;
    message?: string;
    error?: unknown;
}

export const ErrorView: React.FC<ErrorViewProps> = ({ title, subtitle, message, error }) => {
    title ||= 'Error';
    subtitle ||= '';
    message ||= DEFAULT_MESSAGE;
    let stack: string | undefined;

    if (error instanceof ResponseError) {
        title = `${error.response.status}`;
        subtitle = error.response.statusText;
        switch (error.response.status) {
            case 401:
                message = '認証が切れました。再度ログインしてください。';
                break;
            case 403:
                message = 'アクセスが拒否されました。';
                break;
            case 404:
                message = 'お探しのページが見つかりませんでした。';
                break;
        }
    }
    if (import.meta.env.DEV && error instanceof Error) {
        stack = error.stack;
    }

    return (
        <div className="p-4 text-center">
            <p className="mb-2">
                <i className="ti ti-alert-triangle-filled text-7xl text-amber-500" />
            </p>
            <h1 className="text-4xl">{title}</h1>
            {subtitle && <p className="mt-2">{subtitle}</p>}
            <Message>{message}</Message>
            {stack && <pre className="p-4 mt-4 text-left text-sm whitespace-pre-wrap bg-gray-back">{stack}</pre>}
        </div>
    );
};

interface MessageProps {
    children: React.ReactNode;
}

const Message: React.FC<MessageProps> = ({ children }) => (
    <p className="mt-4 pt-4 border-t-1 border-t-gray-border">{children}</p>
);
