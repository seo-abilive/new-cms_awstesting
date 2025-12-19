import { ResourceSort } from '@/utils/components/common/ResourceSort'
import { useModPage } from '../utils/context/ModPageContext'

export const Sort = () => {
    const { config } = useModPage()
    const breads = [{ name: config.name }, { name: '並び替え' }]
    const columns = [{ key: 'title', label: 'タイトル' }]

    return <ResourceSort options={{ breads, columns, config }} />
}
