import { Button as FButton, ButtonGroup as FButtonGroup } from 'flowbite-react'

/**
 * Button component re-exported from flowbite-react.
 *
 * @see {@link https://flowbite-react.com/docs/components/button Flowbite Button Docs}
 * @type {import('flowbite-react').Button}
 */
export const Button = FButton

/**
 * ButtonGroup component re-exported from flowbite-react.
 *
 * @see {@link https://flowbite-react.com/docs/components/button Flowbite Button Docs}
 * @type {import('flowbite-react').ButtonGroup}
 */
export const ButtonGroup = FButtonGroup

export const AdvanceSearchButton = ({ onClear, onApply }) => {
    return (
        <>
            <div className="mt-4 flex gap-2 justify-end">
                <Button
                    size="xs"
                    color="light"
                    outline
                    onClick={onClear}
                    className="dark:text-gray-200"
                >
                    クリア
                </Button>
                <Button
                    size="xs"
                    color="blue"
                    onClick={onApply}
                    data-testid="advanced-apply"
                    outline
                >
                    適用
                </Button>
            </div>
        </>
    )
}
