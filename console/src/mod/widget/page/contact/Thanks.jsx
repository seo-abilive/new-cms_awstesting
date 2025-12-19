import { useEffect } from 'react'
import { useContact } from '../../utils/context/ContactContext'

export const Thanks = () => {
    const { contactSettingData, inputData, setInputData } = useContact()

    useEffect(() => {
        if (Object.keys(inputData).length === 0) {
            navigateTo(`/widget/contact/${token}`)
        }

        setInputData({})
    }, [])

    return (
        <>
            <div dangerouslySetInnerHTML={{ __html: contactSettingData?.contents?.thanks_page }} />
        </>
    )
}
