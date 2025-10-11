import React from 'react';
import {
    CheckinForm,
    CheckinFormErrors,
    CheckinFormValidationError,
    CheckinFormValues,
    SubmitHandler,
} from '../features/checkins/CheckinForm';
import { format } from 'date-fns';
import { ExternalLink } from '../components/ExternalLink';
import { usePostCheckin } from '../api/mutation';
import { ResponseError } from '../api/errors';
import { toast } from 'sonner';
import { useNavigate, useSearchParams } from 'react-router';

export const CheckinCreate: React.FC = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const postCheckin = usePostCheckin();

    const now = new Date();
    const initialValues: Partial<CheckinFormValues> = {
        date: searchParams.get('date')?.replace(/\//g, '-') || format(now, 'yyyy-MM-dd'),
        time: searchParams.get('time') || format(now, 'HH:mm'),
        link: searchParams.get('link') || '',
        tags: searchParams.getAll('tags').flatMap((v) => v.split(' ')) || [],
        note: searchParams.get('note') || '',
        is_private: searchParams.has('is_private', '1'),
        is_too_sensitive: searchParams.has('is_too_sensitive', '1'),
        discard_elapsed_time: searchParams.has('discard_elapsed_time', '1'),
        is_realtime: !(searchParams.has('date') || searchParams.has('time')),
    };

    const handleSubmit: SubmitHandler = async (values) => {
        try {
            const createdCheckin = await postCheckin.mutateAsync({
                checked_in_at: `${values.date.replace(/\//g, '-')}T${values.time}:00+09:00`,
                link: values.link,
                note: values.note,
                tags: values.tags,
                is_private: values.is_private,
                is_too_sensitive: values.is_too_sensitive,
                discard_elapsed_time: values.discard_elapsed_time,
            });
            toast.success('チェックインしました');
            await navigate(`/checkin/${createdCheckin.id}`);
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    const errors: CheckinFormErrors = {};
                    for (const violation of e.error.violations) {
                        const field = violation.field as keyof CheckinFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CheckinFormValidationError(errors);
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
            toast.error('エラーが発生しました');
        }
    };

    return (
        <div className="p-4">
            <h1 className="pb-4 text-3xl border-b-1 border-gray-border">チェックイン</h1>
            <div className="mx-auto lg:max-w-[600px]">
                <CheckinForm mode="create" initialValues={initialValues} onSubmit={handleSubmit} />
            </div>
            <p className="text-center text-sm mt-4">
                <strong>Tips</strong>: ブックマークレットや共有機能で、簡単にチェックインできます！{' '}
                <ExternalLink href="/checkin-tools">使い方はこちら</ExternalLink>
            </p>
        </div>
    );
};
