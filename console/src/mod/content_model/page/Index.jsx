import { markupConfig } from '@/mod/content_model/utils/config'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { HiOutlineNewspaper } from 'react-icons/hi'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { config as fieldConfig } from '@/mod/content_field/utils/config'
import { Link } from 'react-router'
import { useContentModel } from '../utils/context/ContentModelContext'

export const Index = () => {
    const { config, getBreads, replacePath } = useContentModel()
    const breads = getBreads()
    const { navigateTo } = useNavigation()

    const columns = [
        { key: 'title', label: '名前' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    const addDropdownItems = [
        {
            name: 'field',
            onClick: (item, row) => {
                navigateTo(replacePath(fieldConfig.path).replace(':model_id', item.id))
            },
            icon: HiOutlineNewspaper,
        },
        {
            name: '構造化管理',
            onClick: (item, row) => {
                navigateTo(replacePath(markupConfig.path).replace(':model_id', item.id))
            },
            icon: HiOutlineNewspaper,
        },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config,
                    columns,
                    isSort: true,
                    addDropdownItems,
                    addScopedColumns: {
                        title: (item, row, idx) => {
                            return (
                                <td className="p-2" key={idx}>
                                    <Link
                                        to={replacePath(fieldConfig.path).replace(
                                            ':model_id',
                                            item.id
                                        )}
                                        className="underline"
                                    >
                                        {item.title}
                                    </Link>
                                </td>
                            )
                        },
                    },
                }}
            />
        </>
    )
}
