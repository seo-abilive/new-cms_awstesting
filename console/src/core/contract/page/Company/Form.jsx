import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useContractCompany } from '../../utils/context/ContractCompanyContext'
import { useParams } from 'react-router'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { config } = useContractCompany()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]
    const formItem = [
        { title: '企業名', id: 'company_name', required: true },
        { title: 'エイリアス', id: 'alias', required: true, disabled: id ? true : false },
        { title: '郵便番号', id: 'zip_code' },
        { title: '住所', id: 'address' },
        { title: '電話番号', id: 'phone' },
        { title: 'ウェブサイト', id: 'website' },
        {
            title: 'AI APIキー',
            id: 'ai_api_key',
            formType: 'text',
            help_text: 'Gemini APIキーを設定すると、AI添削が使用できます',
        },
        {
            title: 'ステータス',
            id: 'status',
            formType: 'switch',
            default: true,
            position: 'aside',
        },
    ]

    return (
        <>
            <ResourceForm
                options={{
                    breads,
                    config,
                    formItem,
                    id,
                }}
            />
        </>
    )
}

export const New = () => {
    return (
        <>
            <Form pageName={'新規作成'} />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Form pageName={'編集'} />
        </>
    )
}
