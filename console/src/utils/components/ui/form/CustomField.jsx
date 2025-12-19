import { useEffect, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { customConfig } from '@/mod/content_field/utils/config'
import { Spinner } from '@/utils/components/ui/spinner'
import { FormComp } from '@/utils/components/ui/form'

export const CustomField = ({
    defaultValue = {},
    custom_field_id,
    onChange,
    validationErrors = {},
    validationKey = null,
    readonly = false,
    disabled = false,
    contentConfig = null, // Form.jsxから渡される
    ...props
}) => {
    const [values, setValues] = useState(defaultValue !== '' ? defaultValue : {})
    const { data, loading, sendRequest } = useAxios()

    useEffect(() => {
        // カスタムフィールド取得
        if (custom_field_id) {
            ;(async () => {
                await sendRequest({
                    method: 'get',
                    url: `${customConfig.end_point}/${custom_field_id}`,
                })
            })()
        }
    }, [custom_field_id])

    useEffect(() => {
        if (data?.payload?.data) {
            if (Object.keys(defaultValue).length === 0 || defaultValue === '') {
                let newValues = {}
                data?.payload?.data.fields?.map((field) => {
                    newValues[field?.field_id] = {
                        value: '',
                        custom_content_field_id: field?.id,
                    }
                })
                setValues(newValues)
                onChange && onChange(newValues)
            }
        }
    }, [data])

    // 入力更新
    const updated = (key, value, field_id) => {
        const newValues = {
            ...values,
            [key]: { value: value, custom_content_field_id: field_id },
        }
        setValues(newValues)
        onChange && onChange(newValues)
    }

    // バリデーションエラーをフィールドごとにマッピング
    const getFieldErrors = (fieldId) => {
        // カスタムフィールドのエラーは {parentFieldId}.{childFieldId}.value の形式
        const errorKey = `${validationKey ?? props.id}.${fieldId}.value`
        return validationErrors?.[errorKey] || []
    }

    if (loading) {
        return <Spinner />
    }

    return (
        <div className="ms-6">
            {data?.payload?.data.fields?.map((field, idex) => {
                const item = {
                    title: field?.name,
                    id: `${props.id}_${field?.field_id}`,
                    formType: field?.field_type,
                    required: field?.is_required || field?.is_required === 1 ? true : false,
                    placeholder: field?.placeholder,
                    help_text: field?.help_text,
                    items: field?.choices,
                    defaultValue: values?.[field?.field_id]?.value,
                    onChange: (value) => {
                        updated(field?.field_id, value, field?.id)
                    },
                    error: getFieldErrors(field?.field_id)?.[0],
                }

                // content_referenceタイプの場合はエンドポイントを設定
                if (field?.field_type === 'content_reference') {
                    // 参照先ContentModelの情報を渡す
                    item.contentReferenceModel = field?.content_reference || null
                }

                // richtextタイプの場合はAI添削用の情報を設定
                if (field?.field_type === 'richtext' && contentConfig) {
                    item.contentConfig = contentConfig
                    // contentEndpointはcontentConfigから取得
                    if (contentConfig?.end_point) {
                        item.contentEndpoint = contentConfig.end_point
                    }
                }

                return <FormComp item={item} index={idex} readonly={readonly || disabled} />
            })}
        </div>
    )
}
