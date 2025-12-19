import { NumericFormat } from 'react-number-format'

export const NumberInput = ({ defaultValue, onChange = () => {}, className = '', ...props }) => {
    return (
        <NumericFormat
            className={`block w-full border focus:outline-none focus:ring-1 disabled:cursor-not-allowed disabled:opacity-50 border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500 p-2.5 text-sm rounded-lg pr-10 flatpickr-input ${className}`}
            value={defaultValue}
            thousandSeparator={true}
            onValueChange={(values, sourceInfo) => {
                onChange(values.value, values, sourceInfo)
            }}
            {...props}
        />
    )
}
