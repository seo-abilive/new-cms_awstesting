'use client'

export default function Table({ block = {} }) {
    const { columns = 2, items = [] } = block
    return (
        <div className="box_news_parts">
            <table className="c-table">
                <tbody>
                    {items.map((item, index) => (
                        <tr key={index}>
                            {Array.from({ length: columns }).map((_, colIndex) => {
                                const cell =
                                    typeof item.row[colIndex] !== 'undefined'
                                        ? item.row[colIndex]
                                        : { value: '', is_head: false }
                                if (cell.is_head) {
                                    return <th key={colIndex}>{cell.value}</th>
                                } else {
                                    return <td key={colIndex}>{cell.value}</td>
                                }
                            })}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    )
}
