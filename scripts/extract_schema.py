import re
import json

def parse_schema(filepath):
    tables = {}
    current_table = None
    
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    for line in lines:
        line = line.strip()
        
        # Match CREATE TABLE public.table_name (
        m_table = re.match(r'CREATE TABLE public\.([a-zA-Z0-9_]+)\s*\(', line)
        if m_table:
            current_table = m_table.group(1)
            tables[current_table] = {'columns': [], 'fks': []}
            continue
            
        if current_table:
            if line.startswith(');'):
                current_table = None
                continue
                
            # Skip comments and empty lines
            if not line or line.startswith('--'):
                continue
                
            # Column definition
            # e.g. id uuid DEFAULT gen_random_uuid() NOT NULL,
            # name character varying(255) NOT NULL,
            m_col = re.match(r'([a-zA-Z0-9_]+)\s+([^,]+),?', line)
            if m_col:
                col_name = m_col.group(1)
                col_def = m_col.group(2)
                tables[current_table]['columns'].append({
                    'name': col_name,
                    'def': col_def
                })
                
    # Now parse foreign keys which might be ALTER TABLE public.table_name ADD CONSTRAINT ... FOREIGN KEY ...
    for i, line in enumerate(lines):
        line = line.strip()
        m_alter = re.match(r'ALTER TABLE\s+(?:ONLY\s+)?public\.([a-zA-Z0-9_]+)', line)
        if m_alter:
            table_name = m_alter.group(1)
            # Next few lines might have ADD CONSTRAINT ... FOREIGN KEY
            for j in range(i+1, min(i+5, len(lines))):
                subline = lines[j].strip()
                m_fk = re.search(r'FOREIGN KEY\s+\(([^\)]+)\)\s+REFERENCES\s+(?:public\.)?([a-zA-Z0-9_]+)\(([^\)]+)\)', subline)
                if m_fk:
                    if table_name in tables:
                        tables[table_name]['fks'].append({
                            'column': m_fk.group(1),
                            'ref_table': m_fk.group(2),
                            'ref_column': m_fk.group(3)
                        })
                    break

    with open('schema_summary.json', 'w', encoding='utf-8') as f:
        json.dump(tables, f, indent=2)

if __name__ == "__main__":
    parse_schema('schema_dump.sql')
