import { Radio as FRadio, Label as FLabel } from 'flowbite-react'

/**
 * Radio component wrapping flowbite-react's Radio.
 *
 * @param {object} props
 * @param {boolean} [props.vertical=false] - Display options vertically instead of inline.
 * @param {function(string, React.ChangeEvent<HTMLInputElement>): void} [props.onChange] - Callback fired on value change.
 * @param {Array<{ value: string, label: string }>} props.items - Array of radio options.
 * @param {string} props.name - Name attribute for all radio inputs.
 * @param {string} [props.defaultValue] - Default selected value.
 * @returns {JSX.Element}
 */
export const Radio = ({
    vertical = false,
    onChange = () => {},
    items,
    defaultValue,
    id,
    ...props
}) => {
    return (
        <div className={`${vertical ?? 'flex flex-col'} gap-4`}>
            {items.map((item, idx) => {
                return (
                    <div
                        className={`flex item-center gap-2 ${
                            !vertical ? 'inline-flex me-4' : 'mb-2'
                        }`}
                    >
                        <FRadio
                            id={`${id}_${item.value}`}
                            name={id}
                            value={item.value}
                            defaultChecked={defaultValue === item.value}
                            onChange={(e) => {
                                onChange(e.target.value, e)
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
