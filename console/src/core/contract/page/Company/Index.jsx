import {
    HiOutlineDocumentText,
    HiOutlineOfficeBuilding,
    HiOutlinePlus,
    HiOutlineUser,
} from 'react-icons/hi'
import { useContractCompany } from '../../utils/context/ContractCompanyContext'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { config as contractConfig } from '../../utils/config'
import appConfig from '@/config/configLoader'
import { config as userConfig, USER_TYPE } from '@/core/user/utils/config'
import { config as contentModelConfig } from '@/mod/content_model/utils/config'
import { Link } from 'react-router'

export const Index = () => {
    const { config } = useContractCompany()
    const breads = [{ name: config.name }]
    const { navigateTo } = useNavigation()
    const columns = [
        { key: 'company_name', label: '企業名' },
        { key: 'alias', label: 'エイリアス' },
        { key: 'status', label: 'ステータス' },
        { key: 'goto_content_model', label: '', sortable: false },
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
                        status: (item, row, idx) => {
                            return (
                                <td>
                                    {item.status ? 'ON' : 'OFF'}
                                </td>
                            )
                        },
                        goto_content_model: (item, row) => {
                            return (
                                <td className="p-2">
                                    <Link
                                        to={contentModelConfig.path.replace(
                                            ':company_alias',
                                            item.alias
                                        )}
                                        className="underline"
                                    >
                                        コンテンツモデル管理
                                    </Link>
                                </td>
                            )
                        },
                        goto_manage_admin: (item, row) => {
                            return (
                                <td className="p-2">
                                    <Link
                                        to={`/manage/${item.alias}/master/`}
                                        className="underline"
                                        target="_blank"
                                    >
                                        企業管理管理
                                    </Link>
                                </td>
                            )
                        },
                    },
                    addDropdownItems: [
                        {
                            name: 'コンテンツモデル管理へ',
                            icon: HiOutlineDocumentText,
                            onClick: (item, row) => {
                                navigateTo(
                                    contentModelConfig.path
                                        .replace(':company_alias', item.alias)
                                        .replace(':facility_alias', 'master')
                                )
                            },
                        },
                        {
                            name: '企業管理画面へ',
                            icon: HiOutlineOfficeBuilding,
                            onClick: (item, row) => {
                                if (!item?.alias) return
                                const base = appConfig.basename || '/'
                                const baseTrimmed = base.endsWith('/') ? base.slice(0, -1) : base
                                const path = `/manage/${item.alias}/master/`
                                window.open(
                                    `${baseTrimmed}${path}`,
                                    '_blank',
                                    'noopener,noreferrer'
                                )
                            },
                        },
                        {
                            name: '施設一覧',
                            icon: HiOutlineOfficeBuilding,
                            onClick: (item, row) => {
                                navigateTo(`${contractConfig.path}/facility`, {
                                    company: item,
                                })
                            },
                        },
                        {
                            name: '施設登録',
                            icon: HiOutlinePlus,
                            onClick: (item, row) => {
                                navigateTo(`${contractConfig.path}/facility/new`, {
                                    company: item,
                                })
                            },
                        },
                        {
                            name: 'ユーザー登録',
                            icon: HiOutlineUser,
                            onClick: (item, row) => {
                                navigateTo(`${userConfig.path}/new`, {
                                    user_type: USER_TYPE.MANAGE,
                                    company: item,
                                })
                            },
                        },
                    ],
                }}
            />
        </>
    )
}
