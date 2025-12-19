import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useContractFacility } from '../../utils/context/ContractFacilityContext'
import { useParams, useLocation } from 'react-router'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { config, getBreads, company } = useContractFacility()
    const breads = getBreads([{ name: pageName }], { showPath: true })

    const formItem = [
        {
            title: '企業名',
            id: 'company_id',
            required: true,
            formType: 'taxonomy_select',
            endpoint: 'contract/company/resource',
            placeholder: '選択してください',
            keyLabel: 'company_name',
            default: (() => {
                return company ? { value: company.id, label: company.company_name } : null
            })(),
            isCreatable: false,
            onFetch: (data, item) => {
                return { label: item.company?.company_name, value: item.company?.id }
            },
        },
        { title: '施設名', id: 'facility_name', required: true },
        { title: 'エイリアス', id: 'alias', required: true },
        { title: '郵便番号', id: 'zip_code' },
        { title: '住所', id: 'address' },
        { title: '電話番号', id: 'phone' },
        { title: 'ウェブサイト', id: 'website' },
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
