import React from 'react';
import {
    CheckinForm,
    CheckinFormErrors,
    CheckinFormValidationError,
    CheckinFormValues,
    SubmitHandler,
} from '../features/checkins/CheckinForm';
import { format } from 'date-fns';
import { usePatchCheckin } from '../api/mutation';
import { ResponseError } from '../api/errors';
import { toast } from 'sonner';
import { useLoaderData, useNavigate } from 'react-router';
import { LoaderData } from './CheckinEdit.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getCheckinQuery } from '../api/query';

export const CheckinEdit: React.FC = () => {
    const navigate = useNavigate();
    const { id } = useLoaderData<LoaderData>();
    const { data: checkin } = useSuspenseQuery(getCheckinQuery(id));
    const patchCheckin = usePatchCheckin();

    const initialValues: Partial<CheckinFormValues> = {
        date: format(checkin.checked_in_at, 'yyyy-MM-dd'),
        time: format(checkin.checked_in_at, 'HH:mm'),
        link: checkin.link,
        tags: checkin.tags,
        note: checkin.note,
        is_private: checkin.is_private,
        is_too_sensitive: checkin.is_too_sensitive,
        discard_elapsed_time: checkin.discard_elapsed_time,
    };

    const handleSubmit: SubmitHandler = async (values) => {
        try {
            const createdCheckin = await patchCheckin.mutateAsync({
                id: checkin.id,
                body: {
                    checked_in_at: `${values.date.replace(/\//g, '-')}T${values.time}:00+09:00`,
                    link: values.link,
                    note: values.note,
                    tags: values.tags,
                    is_private: values.is_private,
                    is_too_sensitive: values.is_too_sensitive,
                    discard_elapsed_time: values.discard_elapsed_time,
                },
            });
            toast.success('チェックインを修正しました');
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
            <h1 className="pb-4 text-3xl border-b-1 border-gray-border">チェックインの修正</h1>
            <div className="mx-auto lg:max-w-[600px]">
                <CheckinForm mode="edit" initialValues={initialValues} onSubmit={handleSubmit} />
            </div>
        </div>
    );
};
