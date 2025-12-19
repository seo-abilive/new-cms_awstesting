import { Checkbox as FCheckbox, Label as FLabel } from 'flowbite-react'

/**
 * Checkbox component wrapping flowbite-react's Checkbox.
 *
 * @param {object} props
 * @param {boolean} [props.vertical=false] - Display options vertically instead of inline.
 * @param {function(string, React.ChangeEvent<HTMLInputElement>): void} [props.onChange] - Callback fired on value change.
 * @param {Array<{ value: string, label: string }>} props.items - Array of checkbox options.
 * @param {string} props.name - Name attribute for all checkbox inputs.
 * @param {string[]} [props.defaultValue] - Array of default selected values.
 * @returns {JSX.Element}
 */
export const Checkbox = ({
    vertical = false,
    onChange = () => {},
    items,
    defaultValue = [],
    id,
    ...props
}) => {
    let values = [...defaultValue]

    const onChangeHandler = (checked, value) => {
        if (checked) {
            values.push(value)
        } else {
            values = values.filter((v) => v !== value)
        }
    }

    return (
        <div className={`${vertical ?? 'flex flex-col'} gap-4`}>
            {items.map((item, idx) => {
                return (
                    <div
                        className={`flex item-center gap-2 ${
                            !vertical ? 'inline-flex me-4' : 'me-2'
                        }`}
                    >
                        <FCheckbox
                            id={`${id}_${item.value}`}
                            name={id}
                            value={item.value}
                            defaultChecked={values.includes(item.value)}
                            onChange={(e) => {
                                onChangeHandler(e.target.checked, e.target.value)
                                onChange(values, e)
                            }}
                            {...props}
                        />
                        <FLabel htmlFor={`${id}_${item.value}`} className="text-gray-600">
                            {item.label}
                        </FLabel>
                    </div>
                )
            })}
        </div>
    )
}
