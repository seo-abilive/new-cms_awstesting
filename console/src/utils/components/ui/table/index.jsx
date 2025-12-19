import React, { useEffect, useState, useRef } from 'react'
import {
    Table as FTable,
    TableBody as FTableBody,
    TableCell as FTableCell,
    TableHead as FTableHead,
    TableHeadCell as FTableHeadCell,
    TableRow as FTableRow,
    createTheme,
    ThemeProvider,
} from 'flowbite-react'
import {
    HiOutlineArrowUp,
    HiOutlineArrowDown,
    HiOutlineSwitchVertical,
    HiMenu,
} from 'react-icons/hi'
import {
    arrayMove,
    SortableContext,
    useSortable,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable'
import { closestCenter, DndContext, PointerSensor, useSensor, useSensors } from '@dnd-kit/core'
import { CSS } from '@dnd-kit/utilities'
import { restrictToVerticalAxis } from '@dnd-kit/modifiers'

const SkeletonLoading = ({ columns, skeletonRow = 10 }) => {
    return (
        <>
            {Array.from({ length: skeletonRow }).map((item, row) => {
                return (
                    <FTableRow className="bg-white dark:border-gray-700 dark:bg-gray-800" key={row}>
                        {React.Children.toArray(
                            columns.map((column, idx) => {
                                return (
                                    <FTableCell className={'p-3'} key={idx}>
                                        <div className="animate-pulse w-full h-6 bg-gray-300 dark:bg-gray-500 rounded-xs" />
                                    </FTableCell>
                                )
                            })
                        )}
                    </FTableRow>
                )
            })}
        </>
    )
}

/**
 * ListTable component wrapping flowbite-react's Table with theme provider and scoped columns support.
 *
 * @param {object} props
 * @param {Array} props.columns - Column definitions with label and key.
 * @param {Array} props.items - Array of data objects to render as rows.
 * @param {Object.<string, function>} [props.scopedColumns={}] - Optional render functions for specific columns.
 * @param {object} [props.tableProps={ hoverable: true, striped: true }] - Props to pass to the Table component.
 * @param {object} [props.sortConfig={}] - Sort configuration with column and direction.
 * @param {function} [props.onSort] - Callback function when sort is triggered.
 * @returns {JSX.Element}
 */
export const ListTable = ({
    columns = [],
    items = [],
    scopedColumns = {},
    tableProps = { hoverable: true, striped: true },
    sortConfig = {},
    onSort,
    loading = false,
    skeletonRow = 10,
    nodata = null,
}) => {
    const customTheme = createTheme({
        table: {
            wrapper: '',
        },
    })

    const headCellRefs = useRef({})
    const labelRefs = useRef({})
    const [cellWidths, setCellWidths] = useState({})
    const [isTruncated, setIsTruncated] = useState({})

    useEffect(() => {
        if (loading) return

        const updateWidths = () => {
            const widths = {}
            const truncated = {}
            Object.keys(headCellRefs.current).forEach((key) => {
                const element = headCellRefs.current[key]
                if (element) {
                    widths[key] = element.offsetWidth
                }
            })
            Object.keys(labelRefs.current).forEach((key) => {
                const element = labelRefs.current[key]
                if (element) {
                    // scrollWidth > clientWidth の場合、テキストが省略されている
                    truncated[key] = element.scrollWidth > element.clientWidth
                }
            })
            if (Object.keys(widths).length > 0) {
                setCellWidths(widths)
            }
            if (Object.keys(truncated).length > 0) {
                setIsTruncated(truncated)
            }
        }

        // レンダリング後に幅を取得
        const timer = setTimeout(updateWidths, 0)
        return () => clearTimeout(timer)
    }, [columns, loading])

    const handleSort = (column) => {
        if (!onSort || column.sortable === false) return

        let direction = 'asc'
        if (sortConfig.column === column.key) {
            if (sortConfig.direction === 'asc') {
                direction = 'desc'
            } else if (sortConfig.direction === 'desc') {
                // 降順の次はクリア（ソートなし）
                onSort(null, null)
                return
            }
        }
        onSort(column.key, direction)
    }

    const getSortIcon = (column) => {
        if (column.sortable === false) return null

        if (sortConfig.column === column.key) {
            if (sortConfig.direction === 'asc') {
                return <HiOutlineArrowUp className="w-4 h-4 ml-1 flex-shrink-0" />
            } else if (sortConfig.direction === 'desc') {
                return <HiOutlineArrowDown className="w-4 h-4 ml-1 flex-shrink-0" />
            }
        }
        return <HiOutlineSwitchVertical className="w-4 h-4 ml-1 text-gray-400 flex-shrink-0" />
    }

    return (
        <>
            <ThemeProvider theme={customTheme}>
                <FTable {...tableProps}>
                    <FTableHead>
                        <FTableRow>
                            {columns.map((column, idx) => {
                                let props = column._props !== 'undefined' ? column._props : {}
                                const isSortable = column.sortable !== false
                                const cellWidth = cellWidths[idx]
                                const maxWidth = cellWidth
                                    ? `${cellWidth - (isSortable ? 32 : 0)}px`
                                    : 'none'
                                const shouldShowTooltip = isTruncated[idx] === true

                                return (
                                    <FTableHeadCell
                                        key={idx}
                                        ref={(el) => (headCellRefs.current[idx] = el)}
                                        {...props}
                                        className={`${props?.className || ''} ${
                                            isSortable
                                                ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 select-none'
                                                : ''
                                        } overflow-hidden`}
                                        onClick={() => handleSort(column)}
                                    >
                                        <div className="flex items-center justify-between">
                                            <div
                                                className={`relative flex-1 min-w-0 overflow-hidden ${
                                                    shouldShowTooltip ? 'group' : ''
                                                }`}
                                                style={{
                                                    maxWidth:
                                                        maxWidth !== 'none' ? maxWidth : undefined,
                                                }}
                                            >
                                                <span
                                                    ref={(el) => (labelRefs.current[idx] = el)}
                                                    className="block truncate"
                                                >
                                                    {column.label}
                                                </span>
                                                {shouldShowTooltip && (
                                                    <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-800 text-gray-50 text-xs rounded px-2 py-1 z-10 whitespace-nowrap pointer-events-none">
                                                        {column.label}
                                                    </div>
                                                )}
                                            </div>
                                            {isSortable && (
                                                <div className="flex-shrink-0 ml-1">
                                                    {getSortIcon(column)}
                                                </div>
                                            )}
                                        </div>
                                    </FTableHeadCell>
                                )
                            })}
                        </FTableRow>
                    </FTableHead>
                    <FTableBody className="divide-y">
                        {loading && (
                            <>
                                <SkeletonLoading columns={columns} skeletonRow={skeletonRow} />
                            </>
                        )}
                        {!loading && items.length > 0 && (
                            <>
                                {items.map((item, row) => {
                                    return (
                                        <FTableRow
                                            className="bg-white dark:border-gray-700 dark:bg-gray-800"
                                            key={row}
                                        >
                                            {React.Children.toArray(
                                                columns.map((column, idx) => {
                                                    if (
                                                        typeof scopedColumns[column.key] !==
                                                        'undefined'
                                                    ) {
                                                        return scopedColumns[column.key](
                                                            item,
                                                            row,
                                                            idx
                                                        )
                                                    } else {
                                                        return (
                                                            <FTableCell className={'p-3'} key={idx}>
                                                                {item[column.key]}
                                                            </FTableCell>
                                                        )
                                                    }
                                                })
                                            )}
                                        </FTableRow>
                                    )
                                })}
                            </>
                        )}
                    </FTableBody>
                </FTable>
            </ThemeProvider>
            {!loading && items.length === 0 && nodata}
        </>
    )
}

/**
 * 並び替え一覧表示用コンポーネント
 */
export const SortTable = ({
    columns = [],
    defaultItems = [],
    scopedColumns = {},
    tableProps = { hoverable: true, striped: true },
    loading = false,
    skeletonRow = 10,
    nodata = null,
    onChange,
}) => {
    const SortableRow = ({ id, handle, key, children }) => {
        const { attributes, listeners, setNodeRef, transform, transition, isDragging } =
            useSortable({ id })

        const style = {
            transform: CSS.Transform.toString(transform),
            transition,
            background: isDragging ? '#f3f4f6' : undefined,
            opacity: isDragging ? 0.8 : 1,
        }

        return (
            <FTableRow
                className="bg-white dark:border-gray-700 dark:bg-gray-800"
                key={key}
                style={style}
                ref={setNodeRef}
            >
                <FTableCell className={'p-3'}>
                    <span
                        className="cursor-grab text-gray-400 hover:text-gray-600"
                        style={{ touchAction: 'none' }}
                        {...attributes}
                        {...listeners}
                    >
                        <HiMenu size={20} />
                    </span>
                </FTableCell>
                {children}
            </FTableRow>
        )
    }

    const customTheme = createTheme({
        table: {
            wrapper: '',
        },
    })

    const [items, setItems] = useState([])
    const headCellRefs = useRef({})
    const labelRefs = useRef({})
    const [cellWidths, setCellWidths] = useState({})
    const [isTruncated, setIsTruncated] = useState({})

    useEffect(() => {
        setItems(defaultItems)
    }, [defaultItems])

    useEffect(() => {
        if (loading) return

        const updateWidths = () => {
            const widths = {}
            const truncated = {}
            Object.keys(headCellRefs.current).forEach((key) => {
                const element = headCellRefs.current[key]
                if (element) {
                    widths[key] = element.offsetWidth
                }
            })
            Object.keys(labelRefs.current).forEach((key) => {
                const element = labelRefs.current[key]
                if (element) {
                    // scrollWidth > clientWidth の場合、テキストが省略されている
                    truncated[key] = element.scrollWidth > element.clientWidth
                }
            })
            if (Object.keys(widths).length > 0) {
                setCellWidths(widths)
            }
            if (Object.keys(truncated).length > 0) {
                setIsTruncated(truncated)
            }
        }

        // レンダリング後に幅を取得
        const timer = setTimeout(updateWidths, 0)
        return () => clearTimeout(timer)
    }, [columns, loading, items])

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )

    const ids = items.map((c) => c.id)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = items.findIndex((c) => c.id === active.id)
        const newIndex = items.findIndex((c) => c.id === over.id)
        const updated = arrayMove(items, oldIndex, newIndex)
        setItems(updated)

        onChange && onChange(updated)
    }

    return (
        <>
            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragEnd={handleDragEnd}
                modifiers={[restrictToVerticalAxis]}
            >
                <SortableContext items={ids} strategy={verticalListSortingStrategy}>
                    <ThemeProvider theme={customTheme}>
                        <FTable {...tableProps}>
                            <FTableHead>
                                <FTableRow>
                                    <FTableHeadCell className="w-0.5"></FTableHeadCell>
                                    {columns.map((column, idx) => {
                                        let props =
                                            column._props !== 'undefined' ? column._props : {}
                                        const cellWidth = cellWidths[idx]
                                        const maxWidth = cellWidth ? `${cellWidth}px` : 'none'
                                        const shouldShowTooltip = isTruncated[idx] === true

                                        return (
                                            <FTableHeadCell
                                                key={idx}
                                                ref={(el) => (headCellRefs.current[idx] = el)}
                                                {...props}
                                                className={`${
                                                    props?.className || ''
                                                } overflow-hidden`}
                                            >
                                                <div className="flex items-center justify-between">
                                                    <div
                                                        className={`relative flex-1 min-w-0 overflow-hidden ${
                                                            shouldShowTooltip ? 'group' : ''
                                                        }`}
                                                        style={{
                                                            maxWidth:
                                                                maxWidth !== 'none'
                                                                    ? maxWidth
                                                                    : undefined,
                                                        }}
                                                    >
                                                        <span
                                                            ref={(el) =>
                                                                (labelRefs.current[idx] = el)
                                                            }
                                                            className="block truncate"
                                                        >
                                                            {column.label}
                                                        </span>
                                                        {shouldShowTooltip && (
                                                            <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-800 text-gray-50 text-xs rounded px-2 py-1 z-10 whitespace-nowrap pointer-events-none">
                                                                {column.label}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </FTableHeadCell>
                                        )
                                    })}
                                </FTableRow>
                            </FTableHead>
                            <FTableBody className="divide-y">
                                {loading && (
                                    <>
                                        <SkeletonLoading
                                            columns={columns}
                                            skeletonRow={skeletonRow}
                                        />
                                    </>
                                )}
                                {!loading && items.length > 0 && (
                                    <>
                                        {items.map((item, row) => {
                                            return (
                                                <SortableRow id={item.id} key={item.id}>
                                                    {React.Children.toArray(
                                                        columns.map((column, idx) => {
                                                            if (
                                                                typeof scopedColumns[column.key] !==
                                                                'undefined'
                                                            ) {
                                                                return scopedColumns[column.key](
                                                                    item,
                                                                    row,
                                                                    idx
                                                                )
                                                            } else {
                                                                return (
                                                                    <FTableCell
                                                                        className={'p-3'}
                                                                        key={idx}
                                                                    >
                                                                        {item[column.key]}
                                                                    </FTableCell>
                                                                )
                                                            }
                                                        })
                                                    )}
                                                </SortableRow>
                                            )
                                        })}
                                    </>
                                )}
                            </FTableBody>
                        </FTable>
                    </ThemeProvider>
                    {!loading && items.length === 0 && nodata}
                </SortableContext>
            </DndContext>
        </>
    )
}
