import Flatpickr from 'react-flatpickr'
import { Japanese } from 'flatpickr/dist/l10n/ja.js'
import { twMerge } from 'tailwind-merge'
import { HiOutlineCalendar, HiOutlineXCircle } from 'react-icons/hi'
import dayjs from 'dayjs'
import 'dayjs/locale/ja'
import { useId, useEffect } from 'react'

const customInputClasses =
    'block w-full border focus:outline-none focus:ring-1 disabled:cursor-not-allowed disabled:opacity-50 border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500 p-2.5 text-sm rounded-lg'

/**
 * DatePicker component using react-flatpickr.
 *
 * @param {object} props - Props passed to Flatpickr.
 * @param {string|Date} [props.defaultValue] - The initial date value.
 * @param {function(Date): void} [props.onChange] - Callback fired when the date changes.
 * @returns {JSX.Element}
 */
export const DatePicker = ({ defaultValue, onChange = () => {}, className, id, ...props }) => {
    const uniqueId = useId()
    const flatpickrId = id || `datepicker-${uniqueId}`

    // グローバルロケール設定を確実に適用
    useEffect(() => {
        if (typeof window !== 'undefined' && window.flatpickr) {
            window.flatpickr.localize(Japanese)
        }
    }, [])

    const options = {
        locale: Japanese,
        dateFormat: 'Y-m-d',
        time_24hr: true,
        noCalendar: false,
        // 各インスタンスに一意のIDを設定
        id: flatpickrId,
        ...props.options,
    }

    // 時間が有効な場合のフォーマットを決定
    const getDateFormat = (date) => {
        if (options.enableTime) {
            return dayjs(date).format('YYYY-MM-DD HH:mm')
        }
        return dayjs(date).format('YYYY-MM-DD')
    }

    return (
        <>
            <div className="relative w-full">
                <Flatpickr
                    value={defaultValue}
                    onChange={([date]) => {
                        const value = getDateFormat(date)
                        onChange(value, date)
                    }}
                    options={options}
                    className={twMerge(customInputClasses, 'pr-10', className)}
                    {...props}
                />
                {defaultValue ? (
                    <HiOutlineXCircle
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer"
                        onClick={() => onChange(null)}
                    />
                ) : (
                    <HiOutlineCalendar className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                )}
            </div>
        </>
    )
}
