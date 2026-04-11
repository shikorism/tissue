import React from 'react';
import { cn } from '../../lib/cn';
import { Select } from '../../components/ui/Select';

export type SortKey = 'id:asc' | 'id:desc' | 'name:asc' | 'name:desc' | 'updated_at:asc' | 'updated_at:desc';

interface SortKeySelectProps extends Omit<React.HTMLAttributes<HTMLDivElement>, 'onChange'> {
    value: SortKey;
    onChange: (value: SortKey) => void;
}

export const SortKeySelect: React.FC<SortKeySelectProps> = ({ className, value, onChange, ...rest }) => {
    return (
        <div className={cn('relative w-full', className)} {...rest}>
            <i className="ti ti-sort-ascending-letters absolute left-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none" />
            <Select
                className="h-full p-1 text-sm"
                style={{ paddingLeft: '1.75rem' }}
                value={value}
                onChange={(e) => onChange(e.target.value as SortKey)}
            >
                <option value="name:asc">名前 昇順</option>
                <option value="name:desc">名前 降順</option>
                <option value="id:asc">作成日時 昇順</option>
                <option value="id:desc">作成日時 降順</option>
                <option value="updated_at:asc">更新日時 昇順</option>
                <option value="updated_at:desc">更新日時 降順</option>
            </Select>
        </div>
    );
};
