import { ResourceSort } from '@/utils/components/common/ResourceSort'
import { useContentModel } from '../utils/context/ContentModelContext'

export const Sort = () => {
    const { config, getBreads } = useContentModel()
    const breads = getBreads([{ name: '並び替え' }])
    const columns = [{ key: 'title', label: '名前' }]

    return (
        <>
            <ResourceSort options={{ breads, columns, config }} />
        </>
    )
}
