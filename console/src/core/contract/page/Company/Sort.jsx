import { useContractCompany } from '../../utils/context/ContractCompanyContext'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { config } = useContractCompany()
    const breads = [{ name: config.name }, { name: '並び替え' }]
    const columns = [{ key: 'company_name', label: '企業名' }]

    return (
        <ResourceSort
            options={{
                breads,
                columns,
                config,
            }}
        />
    )
}
