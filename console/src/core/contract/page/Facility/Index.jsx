import { useContractFacility } from '../../utils/context/ContractFacilityContext'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { HiOutlineDocumentText, HiOutlineUser } from 'react-icons/hi'
import { config as userConfig, USER_TYPE } from '@/core/user/utils/config'
import { useNavigation } from '@/utils/hooks/useNavigation'
import appConfig from '@/config/configLoader'
import { config as contentModelConfig } from '@/mod/content_model/utils/config'
import { Link } from 'react-router'

export const Index = () => {
    const { config, getBreads, getBaseParams } = useContractFacility()
    const { navigateTo } = useNavigation()
    const breads = getBreads()
    const baseParams = getBaseParams()

    const columns = [
        { key: 'company_name', label: '企業名' },
        { key: 'facility_name', label: '施設名' },
        { key: 'alias', label: 'エイリアス' },
        { key: 'status', label: 'ステータス' },
        { key: 'goto_manage_admin', label: '', sortable: false },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config,
                    columns,
                    isSort: true,
                    addScopedColumns: {
                        company_name: (item, row, idx) => {
                            return <td key={idx}>{item.company?.company_name ?? '紐付けなし'}</td>
                        },
                        status: (item, row, idx) => {
                            return (
                                <td>
                                    {item.status ? 'ON' : 'OFF'}
                                </td>
                            )
                        },
                        goto_manage_admin: (item, row) => {
                            return (
                                <td className="p-2">
                                    <Link
                                        to={`/manage/${item.company.alias}/${item.alias}/`}
                                        className="underline"
                                        target="_blank"
                                    >
                                        施設管理管理
                                    </Link>
                                </td>
                            )
                        },
                    },
                    addDropdownItems: [
                        {
                            name: '施設管理画面へ',
                            icon: HiOutlineUser,
                            onClick: (item, row) => {
                                const companyAlias = item?.company?.alias
                                const facilityAlias = item?.alias
                                if (!companyAlias || !facilityAlias) return
                                const base = appConfig.basename || '/'
                                const baseTrimmed = base.endsWith('/') ? base.slice(0, -1) : base
                                const path = `/manage/${companyAlias}/${facilityAlias}/`
                                window.open(
                                    `${baseTrimmed}${path}`,
                                    '_blank',
                                    'noopener,noreferrer'
                                )
                            },
                        },
                        {
                            name: 'ユーザー登録',
                            icon: HiOutlineUser,
                            onClick: (item, row) => {
                                navigateTo(`${userConfig.path}/new`, {
                                    user_type: USER_TYPE.FACILITY,
                                    company: item.company,
                                    facility: item,
                                })
                            },
                        },
                    ],
                    baseParams,
                }}
            />
        </>
    )
}
