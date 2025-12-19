import { useState, useEffect } from 'react'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Radio } from '@/utils/components/ui/form/Radio'
import { Button } from '@/utils/components/ui/button'

export const AddValidation = ({ defaultValue = [], onChange }) => {
    const [validations, setValidations] = useState(
        Array.isArray(defaultValue) && defaultValue.length > 0 ? defaultValue : []
    )

    // デフォルトエラーメッセージを初期値に設定
    useEffect(() => {
        if (validations.length === 0) {
            return
        }
        const updated = validations.map((validation) => {
            if (!validation.error_message) {
                return {
                    ...validation,
                    error_message: '',
                }
            }
            return validation
        })
        setValidations(updated)
        onChange && onChange(updated)
    }, [])

    // バリデーションタイプのオプション
    const validationTypeOptions = [
        { value: 'phone', label: '電話番号' },
        { value: 'email', label: 'メールアドレス' },
        { value: 'custom', label: 'カスタム' },
    ]

    // バリデーション追加
    const addValidation = () => {
        const newType = 'phone'
        const newValidations = [...validations, { type: newType, error_message: '' }]
        setValidations(newValidations)
        onChange && onChange(newValidations)
    }

    // バリデーション削除
    const removeValidation = (index) => {
        const newValidations = validations.filter((_, i) => i !== index)
        setValidations(newValidations)
        onChange && onChange(newValidations)
    }

    // バリデーション編集
    const updateValidation = (index, key, val) => {
        const newValidations = validations.map((validation, i) => {
            if (i === index) {
                const updated = { ...validation, [key]: val }
                return updated
            }
            return validation
        })
        setValidations(newValidations)
        onChange && onChange(newValidations)
    }

    // カスタムの場合の正規表現パターンを更新
    const updatePattern = (index, val) => {
        const newValidations = validations.map((validation, i) => {
            if (i === index) {
                return { ...validation, pattern: val }
            }
            return validation
        })
        setValidations(newValidations)
        onChange && onChange(newValidations)
    }

    // エラーメッセージを更新
    const updateErrorMessage = (index, val) => {
        const newValidations = validations.map((validation, i) => {
            if (i === index) {
                return { ...validation, error_message: val }
            }
            return validation
        })
        setValidations(newValidations)
        onChange && onChange(newValidations)
    }

    return (
        <>
            <div>
                {validations.map((validation, index) => (
                    <div
                        key={`validation_${index}`}
                        className="mb-4 p-4 border border-gray-200 rounded-lg"
                    >
                        <div className="mb-3">
                            <label className="block mb-2 text-sm font-medium text-gray-700">
                                バリデーションタイプ
                            </label>
                            <Radio
                                key={`radio_${index}_${validation.type}`}
                                id={`validation_type_${index}`}
                                items={validationTypeOptions}
                                defaultValue={validation.type}
                                onChange={(value) => updateValidation(index, 'type', value)}
                            />
                        </div>

                        {validation.type === 'custom' && (
                            <div className="mt-3">
                                <label className="block mb-2 text-sm font-medium text-gray-700">
                                    正規表現
                                </label>
                                <TextInput
                                    defaultValue={validation.pattern || ''}
                                    placeholder="正規表現（例: ^[0-9]{10}$）"
                                    onChange={(value) => updatePattern(index, value)}
                                />
                                <div className="mt-1 text-xs text-gray-500">
                                    PCRE正規表現形式で入力してください
                                </div>
                            </div>
                        )}

                        <div className="mt-3">
                            <label className="block mb-2 text-sm font-medium text-gray-700">
                                エラーメッセージ
                            </label>
                            <TextInput
                                defaultValue={validation.error_message}
                                placeholder="エラーメッセージを入力してください"
                                onChange={(value) => updateErrorMessage(index, value)}
                            />
                        </div>

                        <div className="mt-3 flex justify-end">
                            <Button
                                size="sm"
                                color="red"
                                outline
                                onClick={() => removeValidation(index)}
                            >
                                削除
                            </Button>
                        </div>
                    </div>
                ))}
                <Button size="sm" outline onClick={addValidation}>
                    ＋バリデーションを追加
                </Button>
            </div>
        </>
    )
}
