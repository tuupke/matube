__author__ = 'max'
"""
Listens for notifications about completed jobs. When notification comes in, the file should have been rsynced to
this servers file directory. It is this processes responsibility to remove this entry from the PendingJob table in the database,
add it to the Completed job table, and notify the user to download the file. 
"""