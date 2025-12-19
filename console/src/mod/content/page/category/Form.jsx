import { useParams } from 'react-router'
import { useContent } from '@/mod/content/utils/context/ContentContext'
import { ResourceForm } from '@/utils/components/common/ResourceForm'

const Contents = () => {
    const { getCateConfig } = useContent()
    const config = getCateConfig()
    const { id } = useParams()

    const breads = [
        { name: config.name, path: config.parent_path },
        { name: 'カテゴリ', path: config.path },
        { name: id ? '編集' : '新規作成' },
    ]

    const formItem = [
        { title: 'タイトル', id: 'title', required: true },
        { title: 'エイリアス', id: 'alias' },
    ]

    return (
        <>
            <ResourceForm options={{ breads, config, formItem, id }} />
        </>
    )
}

export const New = () => {
    return (
        <>
            <Contents />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Contents />
        </>
    )
}
