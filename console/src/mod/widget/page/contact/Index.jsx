import { Button } from '@/utils/components/ui/button'
import { FormBuilder } from '@/utils/components/ui/form'
import { useContact } from '../../utils/context/ContactContext'
import { useAxios } from '@/utils/hooks/useAxios'
import config from '@/config/configLoader'
import { useNavigation } from '@/utils/hooks/useNavigation'

export const Index = () => {
    const { contactSettingData, token, inputData, updated } = useContact()
    const { loading, sendRequest, validationErrors } = useAxios({
        baseURL: config.frontEndpointUrl,
    })
    const { navigateTo } = useNavigation()

    const handleSubmit = async () => {
        const result = await sendRequest({
            method: 'post',
            url: `contact/${token}?validate_only=1`,
            data: inputData,
        })

        if (result?.success) {
            navigateTo(`/widget/contact/confirm/${token}`)
        }
    }

    return (
        <>
            <table className="w-full">
                <tbody>
                    {contactSettingData?.contents?.fields?.map((item, index) => {
                        return (
                            <tr key={index}>
                                <th className="w-1/4 text-center bg-gray-100 p-4 border-1">
                                    {item.name}
                                    {item.is_required || item.is_required === 1 ? (
                                        <span className="text-red-600 ml-1">*</span>
                                    ) : (
                                        ''
                                    )}
                                </th>
                                <td className="w-3/4 p-4 border-1">
                                    <FormBuilder
                                        formType={item.field_type}
                                        defaultValue={inputData[item.field_id]}
                                        onChange={(value) => updated(item.field_id, value)}
                                    />
                                    {validationErrors?.[item.field_id] && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {item?.error ||
                                                validationErrors[item.field_id]?.[0] ||
                                                validationErrors[item.field_id]}
                                        </p>
                                    )}
                                </td>
                            </tr>
                        )
                    })}
                </tbody>
            </table>
            <div className="flex justify-center mt-10">
                <Button
                    outline
                    size="lg"
                    className="w-1/3"
                    disabled={loading}
                    onClick={() => handleSubmit()}
                >
                    {loading ? '入力確認中...' : '入力確認へ'}
                </Button>
            </div>
        </>
    )
}
