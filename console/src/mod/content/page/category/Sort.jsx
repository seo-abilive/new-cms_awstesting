import { useContent } from '@/mod/content/utils/context/ContentContext'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { getCateConfig, modelData } = useContent()
    const config = getCateConfig()
    const breads = [
        { name: config.name, path: config.parent_path },
        { name: 'カテゴリ' },
        { name: '並び替え' },
    ]

    const columns = [{ key: 'title', label: 'タイトル' }]

    return <ResourceSort options={{ breads, config, columns }} />
}
