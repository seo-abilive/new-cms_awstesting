import { ToggleSwitch as FToggleSwitch } from 'flowbite-react'

/**
 * Switch component wrapping flowbite-react's ToggleSwitch.
 *
 * @param {object} props - Props passed to FToggleSwitch.
 * @param {boolean} props.defaultValue - Whether the switch is checked (controlled).
 * @param {function(boolean): void} [props.onChange] - Callback fired when the value changes.
 * @param {string} [props.label] - Optional label shown next to the switch.
 * @returns {JSX.Element}
 */
export const Switch = ({
    defaultValue = false,
    onChange = () => {},
    placeholder = '',
    label = '',
    ...props
}) => {
    return (
        <FToggleSwitch
            checked={defaultValue}
            label={placeholder || label}
            onChange={(value) => {
                onChange(value)
            }}
            {...props}
        />
    )
}
