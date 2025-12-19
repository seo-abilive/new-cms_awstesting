import { Select as FSelect } from 'flowbite-react'

/**
 * Select component wrapping flowbite-react's Select.
 *
 * @param {object} props - Props passed to FSelect.
 * @param {string} props.id - ID of the select input.
 * @param {string} [props.name] - Name of the select input.
 * @param {Array<{ value: string, label: string }>} props.items - Array of option items.
 * @param {string} [props.value] - Selected value.
 * @param {function(string, React.ChangeEvent<HTMLSelectElement>): void} [props.onChange] - Callback when selected value changes.
 * @param {boolean} [props.required] - Whether the field is required.
 * @returns {JSX.Element}
 */
export const Select = ({ placeholder = null, onChange = () => {}, items, ...props }) => {
    return (
        <FSelect
            {...props}
            onChange={(e) => {
                onChange(e.target.value, e)
            }}
        >
            {placeholder && <option>{placeholder}</option>}
            {items.map((item) => (
                <option
                    key={item.value}
                    value={item.value}
                    checked={item.value === props.defaultValue}
                >
                    {item.label}
                </option>
            ))}
        </FSelect>
    )
}
