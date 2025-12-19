import { Textarea as FTextarea } from 'flowbite-react'
import { useRef } from 'react'
import { Button } from '../button'
import { HiOutlineLightBulb } from 'react-icons/hi'
import { ProofReading } from '../ai/ProofReading'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

/**
 * Textarea component wrapping flowbite-react's Textarea.
 *
 * @param {object} props - Props passed to FTextarea.
 * @param {string} props.id - ID of the textarea element.
 * @param {string} [props.name] - Name of the textarea field.
 * @param {string} [props.value] - Current value of the textarea.
 * @param {function(string, React.ChangeEvent<HTMLTextAreaElement>): void} [props.onChange] - Callback with value and event.
 * @returns {JSX.Element}
 */
export const Textarea = (props) => {
    const { defaultValue, onChange = () => {}, style = {}, isNotAiUse = false } = props
    const { isAiUse } = useCompanyFacility()
    const proofreadingRef = useRef(null)

    return (
        <>
            {(!isAiUse || isNotAiUse) && <Form {...props} />}
            {isAiUse && !isNotAiUse && (
                <>
                    <div className="relative w-full">
                        <Form
                            {...props}
                            value={defaultValue}
                            style={{ ...style, paddingRight: '6rem' }}
                        />
                        <Button
                            color="light"
                            outline
                            size="xs"
                            className="absolute right-3 top-5 -translate-y-1/2 cursor-pointer"
                            disabled={
                                !defaultValue?.trim() || proofreadingRef?.current?.isProofreading()
                            }
                            onClick={() =>
                                proofreadingRef?.current?.show(defaultValue, defaultValue, false)
                            }
                        >
                            <HiOutlineLightBulb className="inline" style={{ marginRight: '2px' }} />
                            AIアシスト
                        </Button>
                    </div>
                </>
            )}
            {!isNotAiUse && (
                <ProofReading
                    ref={proofreadingRef}
                    onProofread={(text) => {
                        onChange(text)
                    }}
                />
            )}
        </>
    )
}

const Form = ({ onChange = () => {}, ...props }) => {
    return (
        <FTextarea
            onChange={(e) => {
                onChange(e.target.value, e)
            }}
            {...props}
        />
    )
}
