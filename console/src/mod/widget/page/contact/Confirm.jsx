import { useContact } from '../../utils/context/ContactContext'
import { Button } from '@/utils/components/ui/button'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useAxios } from '@/utils/hooks/useAxios'
import config from '@/config/configLoader'
import { useEffect } from 'react'
import { useGoogleReCaptcha } from 'react-google-recaptcha-v3'

export const Confirm = () => {
    const { contactSettingData, token, inputData } = useContact()
    const { loading, sendRequest } = useAxios({ baseURL: config.frontEndpointUrl })
    const { navigateTo } = useNavigation()
    const { executeRecaptcha } = useGoogleReCaptcha()

    useEffect(() => {
        if (Object.keys(inputData).length === 0) {
            navigateTo(`/widget/contact/${token}`)
        }
    }, [])

    const handleSubmit = async () => {
        let recaptchaToken = null

        // reCAPTCHAが有効な場合はトークンを生成
        if (contactSettingData?.contents?.is_recaptcha && executeRecaptcha) {
            try {
                recaptchaToken = await executeRecaptcha('contact_form_submit')
            } catch (error) {
                console.error('reCAPTCHA実行エラー:', error)
            }
        }

        const result = await sendRequest({
            method: 'post',
            url: `contact/${token}`,
            data: {
                ...inputData,
                ...(recaptchaToken && { recaptcha_token: recaptchaToken }),
            },
        })

        if (result?.success) {
            navigateTo(`/widget/contact/thanks/${token}`)
        }
    }

    return (
        <>
            {typeof result !== 'undefined' && result?.success === false && (
                <div className="mb-4 text-red-600">
                    メールの送信に失敗しました。しばらくしてから再度お試しください。
                </div>
            )}
            <table className="w-full">
                <tbody>
                    {contactSettingData?.contents?.fields?.map((item, index) => {
                        return (
                            <tr key={index}>
                                <th className="w-1/4 text-center bg-gray-100 p-4 border-1">
                                    {item.name}
                                </th>
                                <td className="w-3/4 p-4 border-1">
                                    <div style={{ whiteSpace: 'pre-wrap' }}>
                                        {inputData[item.field_id]}
                                    </div>
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
                    className="w-1/4 mr-4"
                    color="gray"
                    onClick={() => navigateTo(`/widget/contact/${token}`)}
                >
                    戻る
                </Button>
                <Button
                    outline
                    size="lg"
                    className="w-1/4"
                    disabled={loading}
                    onClick={() => handleSubmit()}
                >
                    {loading ? '送信中...' : '送信する'}
                </Button>
            </div>
        </>
    )
}
