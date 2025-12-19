import { useContractFacility } from '../../utils/context/ContractFacilityContext'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { config, getBreads, company } = useContractFacility()
    const breads = getBreads([{ name: '並び替え' }])
    const columns = [
        { key: 'company_name', label: '企業名' },
        { key: 'facility_name', label: '施設名' },
    ]

    return (
        <ResourceSort
            options={{
                breads,
                columns,
                config,
                baseParams: { criteria: { company_id: company?.id } },
                addScopedColumns: {
                    company_name: (item, row, idx) => {
                        return <td key={idx}>{item.company?.company_name ?? '紐付けなし'}</td>
                    },
                },
            }}
        />
    )
}
