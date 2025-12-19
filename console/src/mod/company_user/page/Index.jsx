import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useModPage } from '../utils/context/ModPageContext'
import { userTypeOptions } from '@/core/user/utils/config'

export const Index = () => {
    const { config } = useModPage()
    const breads = [{ name: config.name }]
    const columns = [
        { key: 'name', label: '名前' },
        { key: 'email', label: 'メールアドレス' },
        { key: 'user_type', label: 'ユーザータイプ' },
        { key: 'status', label: 'ステータス' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config,
                    columns,
                    addScopedColumns: {
                        user_type: (item, row, idx) => {
                            return (
                                <td>
                                    {
                                        userTypeOptions.find(
                                            (option) => option.value === item.user_type
                                        )?.label
                                    }
                                </td>
                            )
                        },
                        status: (item, row, idx) => {
                            return <td>{item.status ? 'ON' : 'OFF'}</td>
                        },
                    },
                }}
            />
        </>
    )
}
