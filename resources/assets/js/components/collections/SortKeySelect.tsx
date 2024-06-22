import React from 'react';
import classNames from 'classnames';

export type SortKey = 'id:asc' | 'id:desc' | 'name:asc' | 'name:desc' | 'updated_at:asc' | 'updated_at:desc';

interface SortKeySelectProps extends Omit<React.HTMLAttributes<HTMLDivElement>, 'onChange'> {
    value: SortKey;
    onChange: (value: SortKey) => void;
}

export const SortKeySelect: React.FC<SortKeySelectProps> = ({ className, value, onChange, ...rest }) => {
    return (
        <div className={classNames('position-relative', className)} {...rest}>
            <i
                className="ti ti-sort-ascending-letters mr-2 position-absolute text-secondary"
                style={{ top: '22%', left: '0.75rem' }}
            />
            <select
                className="form-control form-control-sm"
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
            </select>
        </div>
    );
};
