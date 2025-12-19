import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { TextInput as FTextInput } from 'flowbite-react'
import { useRef } from 'react'
import { ProofReading } from '../ai/ProofReading'
import { Button } from '../button'
import { HiOutlineLightBulb } from 'react-icons/hi'

/**
 * TextInput component wrapping flowbite-react's TextInput.
 *
 * @param {object} props - Props passed to FTextInput.
 * @param {string} props.id - ID of the input element.
 * @param {string} [props.name] - Name of the input field.
 * @param {string} [props.value] - Current value of the input.
 * @param {function(string, React.ChangeEvent<HTMLInputElement>): void} [props.onChange] - Callback with value and event.
 * @returns {JSX.Element}
 */
export const TextInput = (props) => {
    const { defaultValue, onChange = () => {}, style = {}, isNotAiUse = false } = props
    const { isAiUse } = useCompanyFacility()
    const proofreadingRef = useRef(null)

    return (
        <>
            {(!isAiUse || isNotAiUse) && <Input {...props} />}
            {isAiUse && !isNotAiUse && (
                <>
                    <div className="relative w-full">
                        <Input
                            {...props}
                            value={defaultValue}
                            style={{ ...style, paddingRight: '6rem' }}
                        />
                        <Button
                            color={'light'}
                            outline
                            size="xs"
                            className="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer"
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

const Input = ({ onChange = () => {}, type, autocomplete, ...props }) => {
    // パスワードフィールドの場合は自動補完を無効化
    let autoCompleteValue = autocomplete
    if (!autoCompleteValue && type === 'password') {
        // パスワードフィールドのデフォルトは new-password（新規作成時）
        // ログイン時は明示的に current-password を指定する必要がある
        autoCompleteValue = 'new-password'
    }
    // 機密情報フィールド（APIキー、シークレットキーなど）の場合は off
    if (!autoCompleteValue && (props.id?.includes('secret') || props.id?.includes('key') || props.id?.includes('token'))) {
        autoCompleteValue = 'off'
    }

    // autoCompleteプロパティを構築（値がある場合のみ）
    const inputProps = {
        onChange: (e) => {
            onChange(e.target.value, e)
        },
        type,
        ...props,
    }
    
    // autoComplete値が設定されている場合のみ追加
    if (autoCompleteValue) {
        inputProps.autoComplete = autoCompleteValue
    }

    return <FTextInput {...inputProps} />
}
