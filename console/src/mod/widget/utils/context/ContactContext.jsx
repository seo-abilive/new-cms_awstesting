import { createContext, useContext, useEffect, useState } from 'react'
import { useParams } from 'react-router'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '@/utils/components/ui/spinner'
import config from '@/config/configLoader'
import { GoogleReCaptchaProvider } from 'react-google-recaptcha-v3'

const ContactContext = createContext(undefined)

export const useContact = () => {
    const context = useContext(ContactContext)
    if (context === undefined) {
        throw new Error('useContact must be used within a ContactProvider')
    }
    return context
}

export const ContactProvider = ({ children }) => {
    // 入力内容
    const [inputData, setInputData] = useState({})

    // お問い合わせ設定情報取得
    const { token } = useParams()
    const {
        data: contactSettingData,
        loading: contactSettingLoading,
        sendRequest: fetchContactSetting,
    } = useAxios({ baseURL: config.frontEndpointUrl })

    // bodyのbackgroundColorを透明に設定
    if (typeof document !== 'undefined') {
        document.body.style.backgroundColor = 'transparent'
    }

    useEffect(() => {
        fetchContactSetting({
            method: 'get',
            url: `contact/${token}`,
        })
    }, [token])

    const updated = (key, value) => {
        setInputData({ ...inputData, [key]: value })
    }

    // In the future, module-specific logic can be added here.
    const value = { token, contactSettingData, inputData, updated, setInputData }

    // reCAPTCHA設定を取得
    const isRecaptcha = contactSettingData?.contents?.is_recaptcha
    const recaptchaSiteKey = contactSettingData?.contents?.recaptcha_site_key

    const content = (
        <ContactContext.Provider value={value}>
            {contactSettingLoading && <Spinner />}
            {!contactSettingLoading && <>{children}</>}
        </ContactContext.Provider>
    )

    // reCAPTCHAが有効な場合は、GoogleReCaptchaProviderでラップ
    if (isRecaptcha && recaptchaSiteKey) {
        return (
            <GoogleReCaptchaProvider reCaptchaKey={recaptchaSiteKey}>
                {content}
            </GoogleReCaptchaProvider>
        )
    }

    return content
}
